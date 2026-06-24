<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login/index.php");
    exit();
}

require_once("../../models/Conexion.php");
$conexion = Conexion::conectar();

$sqlVentas = "
    (SELECT 
        o.id AS documento_id, 
        o.fecha AS fecha_venta, 
        CONCAT('Mesa ', o.mesa_id) AS origen, 
        IFNULL((SELECT SUM(d.subtotal) FROM detalle_orden d WHERE d.orden_id = o.id), 0.00) AS total_venta,
        'Salón' AS tipo
     FROM ordenes_mesa o 
     WHERE o.estado = 'Cerrada' AND o.mesa_id <> 99)
    UNION ALL
    (SELECT 
        p.id AS documento_id, 
        p.fecha AS fecha_venta, 
        c.nombres AS origen, 
        p.total AS total_venta,
        'Delivery' AS tipo
     FROM pedidos p
     INNER JOIN clientes c ON p.cliente_id = c.id)
    ORDER BY fecha_venta DESC";

$ventas = $conexion->query($sqlVentas);
$sumaTotalSistema = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Ventas Detallado</title>
    <link rel="stylesheet" href="../css/productos.css">
    <style>
        .tabla-ventas { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .tabla-ventas th { background: #2563eb; color: white; padding: 15px; text-align: left; }
        .tabla-ventas td { padding: 14px 15px; border-bottom: 1px solid #eee; color: #333; }
        .tabla-ventas tr:hover { background: #f8fafc; }
        .badge { padding: 5px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; }
        .badge-salon { background: #e0f2fe; color: #0369a1; }
        .badge-delivery { background: #dcfce7; color: #15803d; }
        .btn-ver-detalle { background: #ff9f43; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-back-dash { background: #64748b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        
        /* MODAL DE DETALLES DE PLATOS */
        .modal-detalle { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-content-det { background: #fff; margin: 12% auto; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); font-family: Arial, sans-serif; }
        .tabla-modal { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .tabla-modal th { border-bottom: 2px solid #ff9f43; padding: 8px; text-align: left; color: #ff9f43; }
        .tabla-modal td { padding: 10px 8px; border-bottom: 1px solid #eee; color: #333; }
        .btn-close-det { background: #ea5455; color: white; border: none; padding: 6px 12px; border-radius: 6px; float: right; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div style="max-width: 1100px; margin: 30px auto; padding: 0 20px;">
    <a href="../dashboard/index.php" class="btn-back-dash">Volver</a>
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>📈 Registro Detallado de Ventas</h1>
        <a href="../../controllers/ExportarVentasPDF.php" target="_blank" style="background: #ea5455; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-right: 15px; display: inline-block;">
            Exportar PDF 📄 de Reporte
        </a>    
        <h2 style="color: #198754; background: #fff; padding: 15px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);" id="txtGranTotal">Cargando Caja...</h2>
    </div>

    <table class="tabla-ventas">
        <thead>
            <tr>
                <th>ID Transacción</th>
                <th>Fecha / Hora</th>
                <th>Tipo</th>
                <th>Origen / Cliente</th>
                <th>Total Recaudado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if($ventas && $ventas->num_rows > 0) { 
                while($v = $ventas->fetch_assoc()) { 
                    $sumaTotalSistema += $v['total_venta'];
            ?>
                <tr>
                    <td>#<?php echo $v['documento_id']; ?></td>
                    <td><?php echo date('d/m/Y g:i A', strtotime($v['fecha_venta'])); ?></td>
                    <td>
                        <span class="badge <?php echo $v['tipo'] == 'Salón' ? 'badge-salon' : 'badge-delivery'; ?>">
                            <?php echo $v['tipo']; ?>
                        </span>
                    </td>
                    <td><strong><?php echo $v['origen']; ?></strong></td>
                    <td style="font-size: 16px; font-weight: bold; color: #198754;">S/ <?php echo number_format($v['total_venta'], 2); ?></td>
                    <td>
                        <button class="btn-ver-detalle" onclick="verDetalleProductos(<?php echo $v['documento_id']; ?>, '<?php echo $v['tipo']; ?>')">🔍 Ver Platos</button>
                    </td>
                </tr>
            <?php } } else { ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; padding: 30px;">Aún no se registran movimientos de caja.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div id="modalPlatos" class="modal-detalle">
    <div class="modal-content-det">
        <button class="btn-close-det" onclick="cerrarModalPlatos()">X</button>
        <h3 style="margin: 0;" id="tituloModal">🧾 Detalle de Consumo</h3>
        <hr style="border: 1px solid #f2f2f2; margin-top:10px;">
        
        <table class="tabla-modal" id="tablaDetalleItems">
            <thead>
                <tr>
                    <th>Cant.</th>
                    <th>Producto / Plato</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('txtGranTotal').innerText = "Total Caja: S/ <?php echo number_format($sumaTotalSistema, 2); ?>";

function verDetalleProductos(id, tipo) {
    document.getElementById('tituloModal').innerText = `🧾 Detalle Orden #${id} (${tipo})`;
    const tbody = document.querySelector('#tablaDetalleItems tbody');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Buscando en cocina...</td></tr>';
    
    // Abrir ventana visual
    document.getElementById('modalPlatos').style.display = 'block';

    // Petición AJAX limpia en segundo plano
    fetch(`../../controllers/ObtenerDetalleVenta.php?id=${id}&tipo=${tipo}`)
        .then(response => response.json())
        .then(productos => {
            tbody.innerHTML = '';
            
            if(productos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#999;">No se encontraron platos vinculados.</td></tr>';
                return;
            }

            productos.forEach(p => {
                tbody.innerHTML += `
                    <tr>
                        <td><strong>${p.cantidad}x</strong></td>
                        <td>${p.nombre}</td>
                        <td>S/ ${parseFloat(p.precio).toFixed(2)}</td>
                        <td style="font-weight:bold; color:#198754;">S/ ${parseFloat(p.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error("Error:", error);
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:red;">Error al cargar datos.</td></tr>';
        });
}

function cerrarModalPlatos() {
    document.getElementById('modalPlatos').style.display = 'none';
}
</script>

</body>
</html>