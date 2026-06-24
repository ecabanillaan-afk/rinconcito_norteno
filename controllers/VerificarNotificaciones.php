<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

// Buscamos si hay alguna orden donde los platos pasaron a estado 'Listo'
// Hacemos un INNER JOIN con mesas para saber exactamente el NÚMERO de la mesa
$sql = "SELECT d.id, m.numero AS numero_mesa, p.nombre 
        FROM detalle_orden d
        INNER JOIN ordenes_mesa o ON d.orden_id = o.id
        INNER JOIN mesas m ON o.mesa_id = m.id
        INNER JOIN productos p ON d.producto_id = p.id
        WHERE d.estado_kitchen = 'Listo' 
        LIMIT 1";

$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $detalle_id = $fila['id'];
    
    // Cambiamos a 'Notificado' para que la alerta solo salga UNA VEZ
    $conexion->query("UPDATE detalle_orden SET estado_kitchen = 'Notificado' WHERE id = '$detalle_id'");

    echo json_encode([
        'hay_notificacion' => true,
        'mesa_numero' => $fila['numero_mesa'],
        'producto' => $fila['nombre']
    ]);
} else {
    echo json_encode(['hay_notificacion' => false]);
}
exit();