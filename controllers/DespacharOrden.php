<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

// Verificar que llegue el ID de la orden
$orden_id = $_POST['orden_id'] ?? null;

if (!$orden_id) {
    echo json_encode(['success' => false, 'error' => 'No se recibió el ID de la orden']);
    exit();
}

// ACTUALIZACIÓN DOBLE: 
// 1. estado_cocina = 'Listo' (Para que desaparezca de la pantalla del chef)
// 2. estado_kitchen = 'Listo' (Para que salte el aviso Toast en las mesas)
$sqlActualizar = "UPDATE detalle_orden d
                  INNER JOIN productos p ON d.producto_id = p.id
                  SET d.estado_cocina = 'Listo', 
                      d.estado_kitchen = 'Listo' 
                  WHERE d.orden_id = '$orden_id' 
                  AND p.categoria != 'Bebidas'";

if ($conexion->query($sqlActualizar)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar base de datos: ' . $conexion->error]);
}
exit();