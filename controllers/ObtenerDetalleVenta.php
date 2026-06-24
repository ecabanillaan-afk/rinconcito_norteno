<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once("../models/Conexion.php");
$conexion = Conexion::conectar();

$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null;

if (!$id || !$tipo) {
    echo json_encode([]);
    exit();
}
    
if ($tipo === 'Delivery') {
    // 1. Si es delivery, el $id es el ID del pedido. 
    // Buscamos en detalle_orden los platos que corresponden a la orden virtual de la mesa 99 
    // creados exactamente en la misma fecha/segundo que ese pedido.
    $sql = "SELECT d.cantidad, p.nombre, d.precio, d.subtotal 
            FROM detalle_orden d
            INNER JOIN productos p ON d.producto_id = p.id
            WHERE d.orden_id = (
                SELECT o.id 
                FROM ordenes_mesa o 
                WHERE o.mesa_id = 99 
                AND o.fecha = (SELECT pe.fecha FROM pedidos pe WHERE pe.id = '$id')
                LIMIT 1
            )";
} else {
    // 2. Si es Salón, el $id es directamente el ID de la orden_id de la mesa
    $sql = "SELECT d.cantidad, p.nombre, d.precio, d.subtotal 
            FROM detalle_orden d
            INNER JOIN productos p ON d.producto_id = p.id
            WHERE d.orden_id = '$id'";
}

$resultado = $conexion->query($sql);
$items = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $items[] = $fila;
    }
}

echo json_encode($items);
exit();