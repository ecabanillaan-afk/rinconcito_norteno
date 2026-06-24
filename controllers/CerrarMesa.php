<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once("../models/Conexion.php");
$conexion = Conexion::conectar();

if (isset($_POST['mesa_id'])) {
    $mesa_id = $_POST['mesa_id'];

    // 1. Obtener el ID de la orden abierta y calcular su total acumulado
    $sqlOrden = "SELECT id FROM ordenes_mesa WHERE mesa_id='$mesa_id' AND estado='Abierta'";
    $resOrden = $conexion->query($sqlOrden);
    
    if ($resOrden && $resOrden->num_rows > 0) {
        $orden = $resOrden->fetch_assoc();
        $orden_id = $orden['id'];
        
        // Calcular el total real sumando el detalle_orden de esta mesa
        $sqlTotal = "SELECT SUM(subtotal) as total_cuenta FROM detalle_orden WHERE orden_id='$orden_id'";
        $resTotal = $conexion->query($sqlTotal);
        $filaTotal = $resTotal->fetch_assoc();
        $total_final = $filaTotal['total_cuenta'] ?? 0.00;
        
        // Guardar el total cobrado en la misma orden antes de cerrarla (asegúrate de que tu tabla ordenes_mesa soporte guardar totales o lo leeremos directamente de los detalles)
    }

    // 2. Cambiar el estado de la mesa a 'Libre'
    $conexion->query("UPDATE mesas SET estado='Libre' WHERE id='$mesa_id'");

    // 3. Cambiar el estado de la orden abierta a 'Cerrada'
    $conexion->query("UPDATE ordenes_mesa SET estado='Cerrada' WHERE mesa_id='$mesa_id' AND estado='Abierta'");

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Falta el ID de la mesa']);
}
exit();