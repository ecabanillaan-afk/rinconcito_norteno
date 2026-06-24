<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

date_default_timezone_set('America/Lima');

require_once("../models/Conexion.php");
$conexion = Conexion::conectar();

$conexion->query("SET time_zone = '-05:00'");

$filtro = $_GET['filtro'] ?? 'dia';

switch ($filtro) {
    case 'semana':
        $condicion_salon = "o.fecha >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND o.estado = 'Cerrada'";
        $condicion_delivery = "o.fecha >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND o.mesa_id = 99";
        // Petición extra para sumar el costo de envío en la semana
        $sqlDeliveryExtra = "SELECT SUM(costo_delivery) AS total_envios FROM pedidos WHERE fecha >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        break;
    case 'mes':
        $condicion_salon = "o.fecha >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND o.estado = 'Cerrada'";
        $condicion_delivery = "o.fecha >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND o.mesa_id = 99";
        $sqlDeliveryExtra = "SELECT SUM(costo_delivery) AS total_envios FROM pedidos WHERE fecha >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    case 'anio':
        $condicion_salon = "o.fecha >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND o.estado = 'Cerrada'";
        $condicion_delivery = "o.fecha >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND o.mesa_id = 99";
        $sqlDeliveryExtra = "SELECT SUM(costo_delivery) AS total_envios FROM pedidos WHERE fecha >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        break;
    case 'dia':
    default:
        $condicion_salon = "DATE(o.fecha) = CURDATE() AND o.estado = 'Cerrada'";
        $condicion_delivery = "DATE(o.fecha) = CURDATE() AND o.mesa_id = 99";
        $sqlDeliveryExtra = "SELECT SUM(costo_delivery) AS total_envios FROM pedidos WHERE DATE(fecha) = CURDATE()";
        break;
}

// 1. OBTENER EL RANKING DE PRODUCTOS (Para los gráficos)
$sql = "SELECT nombre, SUM(cantidad) AS total_vendido, SUM(subtotal) AS total_recaudado
        FROM (
            SELECT p.nombre, d.cantidad, d.subtotal
            FROM detalle_orden d
            INNER JOIN productos p ON d.producto_id = p.id
            INNER JOIN ordenes_mesa o ON d.orden_id = o.id
            WHERE $condicion_salon AND o.mesa_id <> 99
            
            UNION ALL
            
            SELECT p.nombre, d.cantidad, d.subtotal
            FROM detalle_orden d
            INNER JOIN productos p ON d.producto_id = p.id
            INNER JOIN ordenes_mesa o ON d.orden_id = o.id
            WHERE $condicion_delivery
        ) AS ventas_totales
        GROUP BY nombre
        ORDER BY total_vendido DESC
        LIMIT 7";

$resultado = $conexion->query($sql);
$datos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = [
            'nombre' => $fila['nombre'],
            'total_vendido' => (int)$fila['total_vendido'],
            'total_recaudado' => (float)$fila['total_recaudado']
        ];
    }
}

// 2. ¡EL TRUCO DE LA CAJA REAL!: Sumar el costo_delivery acumulado directamente de la tabla pedidos
$resEnvios = $conexion->query($sqlDeliveryExtra);
$filaEnvios = $resEnvios->fetch_assoc();
$total_envios_dinero = (float)($filaEnvios['total_envios'] ?? 0.00);

// Si hubo ingresos por envío, lo agregamos como una fila virtual al final del JSON para que sume en la caja
if ($total_envios_dinero > 0) {
    $datos[] = [
        'nombre' => 'Ingresos por Costo de Delivery',
        'total_vendido' => 0, // No cuenta como plato físico vendido
        'total_recaudado' => $total_envios_dinero
    ];
}

echo json_encode($datos);
exit();