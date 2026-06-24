<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../views/login/index.php");
    exit();
}

require_once("../models/Conexion.php");
$conexion = Conexion::conectar();

// 1. Obtener los mismos datos unificados de la caja de hoy
$sqlVentas = "
    (SELECT 
        o.id AS documento_id, 
        o.fecha AS fecha_venta, 
        CONCAT('Mesa ', o.mesa_id) AS origen, 
        IFNULL((SELECT SUM(d.subtotal) FROM detalle_orden d WHERE d.orden_id = o.id), 0.00) AS total_venta,
        'Salon' AS tipo
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
$sumaTotal = 0;
$totalTransacciones = 0;

// Configurar cabeceras de descarga automática para el navegador
header("Content-Type: application/vnd.ms-word"); // Forzar renderizado estructurado limpio
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=Reporte_Ventas_".date('d-m-Y').".doc");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; margin: 20px; }
        .documento-box { border: 2px solid #333; padding: 15px; border-radius: 4px; }
        .header-tabla { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-tabla td { border: 1px solid #333; padding: 10px; }
        .titulo-comprobante { font-size: 20px; font-weight: bold; text-align: center; letter-spacing: 1px; }
        
        .datos-empresa { font-size: 12px; line-height: 1.5; }
        
        .tabla-productos { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .tabla-productos th { background: #f2f2f2; border: 1px solid #333; padding: 8px; font-size: 13px; text-align: left; }
        .tabla-productos td { border: 1px solid #333; padding: 8px; font-size: 12px; }
        
        .totales-box { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .totales-box td { border: 1px solid #333; padding: 8px; font-size: 13px; }
    </style>
</head>
<body>

<div class="documento-box">
    
    <table class="header-tabla">
        <tr>
            <td width="30%" align="center" style="vertical-align: middle;">
                <img src="../views/css/img/cevi.png" width="100" height="100" alt="Logo">
            </td>
            <td width="70%" class="titulo-comprobante">
                REPORTE DETALLADO DE VENTAS<br>
                <span style="font-size: 12px; font-weight: normal;">RINCONCITO NORTEÑO S.R.L.</span>
            </td>
        </tr>
    </table>

    <table class="header-tabla">
        <tr>
            <td width="60%" class="datos-empresa">
                <strong>EMPRESA:</strong> Cevichería Rinconcito Norteño<br>
                <strong>DIRECCIÓN:</strong> Av. Principal Del Sabor Norteño S/N<br>
                <strong>ENCARGADO DE CAJA:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?><br>
                <strong>PAÍS:</strong> Perú
            </td>
            <td width="40%" class="datos-empresa" style="vertical-align: top;">
                <strong>FECHA EMISIÓN:</strong> <?php echo date('d / m / Y'); ?><br>
                <strong>HORA REPORTE:</strong> <?php echo date('g:i A'); ?><br>
                <strong>MONEDA:</strong> Soles (S/.)
            </td>
        </tr>
    </table>

    <table class="tabla-productos">
        <thead>
            <tr>
                <th width="15%">ID TRANS.</th>
                <th width="30%">FECHA / HORA DE VENTA</th>
                <th width="20%">TIPO</th>
                <th width="20%">ORIGEN / CLIENTE</th>
                <th width="15%">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if($ventas && $ventas->num_rows > 0) { 
                while($v = $ventas->fetch_assoc()) { 
                    $sumaTotal += $v['total_venta'];
                    $totalTransacciones++;
            ?>
                <tr>
                    <td align="center">#<?php echo $v['documento_id']; ?></td>
                    <td><?php echo date('d/m/Y g:i A', strtotime($v['fecha_venta'])); ?></td>
                    <td><?php echo $v['tipo']; ?></td>
                    <td><strong><?php echo htmlspecialchars($v['origen']); ?></strong></td>
                    <td align="right" style="font-weight: bold;">S/ <?php echo number_format($v['total_venta'], 2); ?></td>
                </tr>
            <?php 
                } 
            } else { 
            ?>
                <tr>
                    <td colspan="5" align="center" style="padding: 20px; color: #777;">Sin movimientos registrados.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table class="totales-box">
        <tr>
            <td width="70%" align="right"><strong>NÚMERO TOTAL DE ÓRDENES PROCESADAS:</strong></td>
            <td width="30%" align="center"><strong><?php echo $totalTransacciones; ?> bultos/pedidos</strong></td>
        </tr>
        <tr>
            <td align="right" style="background: #fdfdfd;"><strong>VALOR NETO EN CAJA:</strong></td>
            <td align="right" style="font-weight: bold; color: #15803d; font-size: 16px;">S/ <?php echo number_format($sumaTotal, 2); ?></td>
        </tr>
    </table>

</div>

</body>
</html>