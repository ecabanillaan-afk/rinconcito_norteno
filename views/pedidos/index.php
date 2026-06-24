<?php
session_start();
require_once "../../models/Conexion.php";
$conexion = Conexion::conectar();

$sqlPedidos = "SELECT p.*, c.nombres, c.telefono, c.direccion 
               FROM pedidos p 
               INNER JOIN clientes c ON p.cliente_id = c.id 
               ORDER BY p.fecha DESC";
$resultado = $conexion->query($sqlPedidos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🛵 Panel de Pedidos Delivery</title>
    <link rel="stylesheet" href="../css/mesas.css">
    <link rel="stylesheet" href="../css/pedidos.css"> </head>
<body>

<div class="panel-delivery">
    
    <div class="header-panel">
        <div>
            <h1>🛵 Control de Pedidos & Delivery</h1>
            <p style="color: #a4a4b2; margin: 5px 0 0 0;">Historial y estados en tiempo real</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="../dashboard/index.php" class="btn-nuevo-pedido" style="background:#3e3e4a;">Volver</a>
            <a href="nuevo.php" class="btn-nuevo-pedido">➕ Nuevo Pedido</a>
        </div>
    </div>

    <table class="tabla-delivery">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente / Teléfono</th>
                <th>Fecha / Hora</th>
                <th>Método Pago</th>
                <th>Delivery</th>
                <th>Total General</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($resultado && $resultado->num_rows > 0) {
                while($p = $resultado->fetch_assoc()) { 
            ?>
                <tr>
                    <td><strong>#<?php echo $p['id']; ?></strong></td>
                    <td>
                        <span style="display:block; font-weight:bold; font-size:15px; color:#fff;"><?php echo $p['nombres']; ?></span>
                        <span style="display:block; color:#a4a4b2; font-size:13px; margin-top:2px;">📞 <?php echo $p['telefono']; ?></span>
                        <span style="display:block; color:#ff9f43; font-size:13px; margin-top:4px; font-style:italic;">📍 <?php echo !empty($p['direccion']) ? $p['direccion'] : 'Recojo en local'; ?></span>
                    </td>
                    <td><?php echo date('d/m/Y g:i a', strtotime($p['fecha'])); ?></td>
                    <td>
                        <span class="badge-pago">
                            <?php 
                                if($p['metodo_pago'] == 'Efectivo') echo '💵 Efectivo';
                                elseif($p['metodo_pago'] == 'Yape') echo '📱 Yape/Plin';
                                else echo '💳 Tarjeta';
                            ?>
                        </span>
                    </td>
                    <td>S/ <?php echo number_format($p['costo_delivery'], 2); ?></td>
                    <td style="color:#28c76f; font-weight:bold; font-size:16px;">S/ <?php echo number_format($p['total'], 2); ?></td>
                    <td>
                        <?php if($p['estado_pedido'] == 'En proceso'): ?>
                            <span class="badge badge-proceso">⏳ En proceso</span>
                        <?php else: ?>
                            <span class="badge badge-entregado">🛵 Entregado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="../../controllers/CambiarEstadoDelivery.php?id=<?php echo $p['id']; ?>&estado=<?php echo $p['estado_pedido']; ?>" class="btn-accion">
                            🔄 Cambiar Estado
                        </a>
                    </td>
                </tr>
            <?php 
                }
            } else { 
            ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#a4a4b2;">
                        No hay pedidos registrados en el sistema. ¡Empieza creando uno!
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>