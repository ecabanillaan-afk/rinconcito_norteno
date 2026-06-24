<?php
date_default_timezone_set('America/Lima');

require_once("../models/Conexion.php");
$conexion = Conexion::conectar();

// Asegurar que la zona horaria en la sesión de MySQL coincida con Perú
$conexion->query("SET time_zone = '-05:00'");

$formato = $_GET['formato'] ?? 'excel';
$filtro = $_GET['filtro'] ?? 'dia';

// Configurar los títulos según el periodo activo
switch ($filtro) {
    case 'semana': 
        $W = "WHERE o.fecha >= DATE_SUB(NOW(), INTERVAL 1 WEEK)"; 
        $tit = "DE ESTA SEMANA"; 
        break;
    case 'mes': 
        $W = "WHERE o.fecha >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"; 
        $tit = "DE ESTE MES"; 
        break;
    case 'anio': 
        $W = "WHERE o.fecha >= DATE_SUB(NOW(), INTERVAL 1 YEAR)"; 
        $tit = "DE ESTE AÑO"; 
        break;
    default: 
        $W = "WHERE DATE(o.fecha) = CURDATE()"; 
        $tit = "DEL DÍA DE HOY"; 
        break;
}

// Consulta maestra unificada para el ranking
$sql = "SELECT p.nombre, SUM(d.cantidad) AS total_vendido, SUM(d.subtotal) AS total_recaudado
        FROM detalle_orden d
        INNER JOIN productos p ON d.producto_id = p.id
        INNER JOIN ordenes_mesa o ON d.orden_id = o.id
        $W 
        GROUP BY p.id 
        ORDER BY total_vendido DESC";

$res = $conexion->query($sql);

// SI EL USUARIO ELIGIÓ EXCEL: Enviamos las cabeceras de descarga directa
if ($formato === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Reporte_Ventas_$filtro.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM para mantener tildes en Excel
?>
    <table border="1" style="font-family:Arial, sans-serif; border-collapse:collapse; width:100%;">
        <tr style="background:#2563eb; color:white; font-weight:bold;">
            <th colspan="3" style="padding:12px; font-size:16px; text-align:center;">RANKING DE RECAUDACIÓN (<?php echo $tit; ?>)</th>
        </tr>
        <tr style="background:#f2f2f2; font-weight:bold;">
            <th style="padding:8px; text-align:left;">Plato / Producto</th>
            <th style="padding:8px; text-align:center;">Cantidad Pedida</th>
            <th style="padding:8px; text-align:right;">Ingreso Neto</th>
        </tr>
        <?php 
        $granTotal = 0; $unidadesTotal = 0;
        while($r = $res->fetch_assoc()) { 
            $granTotal += $r['total_recaudado']; $unidadesTotal += $r['total_vendido'];
        ?>
        <tr>
            <td style="padding:8px;"><?php echo htmlspecialchars($r['nombre']); ?></td>
            <td style="padding:8px;" align="center"><?php echo $r['total_vendido']; ?> und.</td>
            <td style="padding:8px;" align="right">S/ <?php echo number_format($r['total_recaudado'], 2); ?></td>
        </tr>
        <?php } ?>
        <tr style="font-weight:bold; background:#e2e8f0;">
            <td align="right" style="padding:10px;">TOTAL GENERAL:</td>
            <td align="center" style="padding:10px;"><?php echo $unidadesTotal; ?> items</td>
            <td align="right" style="padding:10px;">S/ <?php echo number_format($granTotal, 2); ?></td>
        </tr>
    </table>
<?php
    exit();
} 

// SI EL USUARIO ELIGIÓ PDF: Renderizamos la plantilla visual estructurada (Estilo Lista de Empaque)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas PDF - Rinconcito Norteño</title>
    <style>
        body { font-family: 'Arial', sans-serif; color: #111; margin: 10px; background-color: #fff; }
        .documento-box { border: 2px solid #000; padding: 15px; border-radius: 2px; box-shadow: none; max-width: 850px; margin: 0 auto; }
        .header-tabla { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .header-tabla td { border: 2px solid #000; padding: 12px; }
        .titulo-comprobante { font-size: 18px; font-weight: bold; text-align: center; letter-spacing: 0.5px; line-height: 1.4; }
        
        .datos-empresa { font-size: 11px; line-height: 1.6; }
        
        .tabla-productos { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .tabla-productos th { background: #f2f2f2; border: 2px solid #000; padding: 10px; font-size: 12px; font-weight: bold; }
        .tabla-productos td { border: 2px solid #000; padding: 10px; font-size: 12px; vertical-align: middle; }
        
        .totales-box { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .totales-box td { border: 2px solid #000; padding: 10px; font-size: 12px; font-weight: bold; }
        
        /* Ocultar botones al momento de imprimir en papel o guardar en PDF */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .documento-box { border: 2px solid #000; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 20px; font-family: Arial;">
    <button onclick="window.print();" style="background: #ea5455; color: white; border: none; padding: 12px 25px; font-size: 15px; font-weight: bold; border-radius: 6px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
        🖨️ CONFIRMAR IMPRESIÓN / GUARDAR COMO PDF
    </button>
    <button onclick="window.close();" style="background: #64748b; color: white; border: none; padding: 12px 20px; font-size: 15px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-left: 10px;">
        Cerrar Vista
    </button>
</div>

<div class="documento-box">
    
    <table class="header-tabla">
        <tr>
            <td width="25%" align="center" style="vertical-align: middle;">
                <img src="../css/img/cevi.png" width="90" height="90" alt="LOGO" onerror="this.style.display='none';">
                <span style="font-size: 11px; font-weight: bold; display: block; margin-top: 5px;">RINCONCITO NORTEÑO</span>
            </td>
            <td width="75%" class="titulo-comprobante">
                LISTA DE EMPAQUE / REPORTE DE RENDIMIENTO<br>
                <span style="font-size: 13px; font-weight: normal; font-style: italic;">REFERIDA A LA RECAUDACIÓN DE CAJA <?php echo $tit; ?></span>
            </td>
        </tr>
    </table>

    <table class="header-tabla">
        <tr>
            <td width="55%" class="datos-empresa">
                <strong>ESTABLECIMIENTO:</strong> Rinconcito Norteño S.R.L.<br>
                <strong>DIRECCIÓN:</strong> Av. Principal Del Sabor Marino S/N<br>
                <strong>CIUDAD:</strong> Lambayeque / Lima<br>
                <strong>PAÍS:</strong> Perú
            </td>
            <td width="45%" class="datos-empresa" style="vertical-align: top;">
                <strong>FECHA DE EMISIÓN:</strong> <?php echo date('d \d\e F \d\e Y'); ?><br>
                <strong>HORA REPORTE:</strong> <?php echo date('g:i A'); ?><br>
                <strong>MONEDA OFICIAL:</strong> Soles (S/.)
            </td>
        </tr>
    </table>

    <table class="tabla-productos">
        <thead>
            <tr>
                <th width="15%" style="text-align: center;">CANTIDAD</th>
                <th width="60%" style="text-align: left;">DESCRIPCIÓN DEL CONTENIDO (PLATO / PRODUCTO)</th>
                <th width="25%" style="text-align: right;">PESO COMERCIAL / INGRESO NETO</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $granTotal = 0;
            $unidadesTotal = 0;
            if($res && $res->num_rows > 0) { 
                while($r = $res->fetch_assoc()) { 
                    $granTotal += $r['total_recaudado'];
                    $unidadesTotal += $r['total_vendido'];
            ?>
                <tr>
                    <td align="center" style="font-size: 14px; font-weight: bold;"><?php echo $r['total_vendido']; ?></td>
                    <td style="text-transform: uppercase; letter-spacing: 0.3px; font-weight: 5px;"><?php echo htmlspecialchars($r['nombre']); ?></td>
                    <td align="right" style="font-weight: bold; font-size: 13px; color: #000;">S/ <?php echo number_format($r['total_recaudado'], 2); ?></td>
                </tr>
            <?php 
                } 
            } else { 
            ?>
                <tr>
                    <td colspan="3" align="center" style="padding: 25px; color: #555; font-style: italic;">No se registran comandas ni ventas cerradas en el rango de tiempo seleccionado.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table class="totales-box">
        <tr>
            <td width="70%" align="right" style="background:#fdfdfd; padding-right:15px;">NUMERO TOTAL DE BULTOS / ITEMS VENDIDOS:</td>
            <td width="30%" align="center" style="font-size: 13px;"><?php echo $unidadesTotal; ?> unidades</td>
        </tr>
        <tr>
            <td align="right" style="background:#fdfdfd; padding-right:15px;">VALOR INGRESO NETO TOTAL TOTAL:</td>
            <td align="right" style="font-size: 15px; font-weight: bold; padding-right:10px;">S/ <?php echo number_format($granTotal, 2); ?></td>
        </tr>
    </table>

</div>

<script>
window.onload = function() {
    setTimeout(function() {
        window.print();
    }, 300);
};
</script>

</body>
</html>