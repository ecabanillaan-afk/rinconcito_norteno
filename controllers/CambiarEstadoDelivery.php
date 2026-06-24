<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

$id = $_GET['id'] ?? null;
$estado_actual = $_GET['estado'] ?? null;

if ($id && $estado_actual) {
    // Si estaba En proceso, pasa a Pedido entregado. Si no, vuelve a En proceso.
    $nuevo_estado = ($estado_actual == 'En proceso') ? 'Pedido entregado' : 'En proceso';
    
    $sql = "UPDATE pedidos SET estado_pedido = '$nuevo_estado' WHERE id = '$id'";
    $conexion->query($sql);
}

// Redirecciona automáticamente de vuelta al panel de control
header("Location: ../views/pedidos/index.php");
exit();