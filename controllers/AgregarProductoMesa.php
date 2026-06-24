<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Garantizamos que la respuesta siempre regrese como JSON limpio
header('Content-Type: application/json');

require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

// Captura flexible de las variables enviadas por tu JavaScript
$mesa_id = $_POST['mesa_id'] ?? $_POST['id_mesa'] ?? $_POST['id'] ?? null;
$producto_id = $_POST['producto_id'] ?? $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? null;

// Si falta algún dato, respondemos con el error en JSON para no romper la consola
if (!$mesa_id || !$producto_id || !$cantidad) {
    echo json_encode([
        'success' => false, 
        'error' => 'Datos incompletos',
        'recibido' => ['mesa_id' => $mesa_id, 'producto_id' => $producto_id, 'cantidad' => $cantidad]
    ]);
    exit();
}

// 1. BUSCAR SI LA MESA YA TIENE UNA ORDEN ABIERTA
$sqlOrden = "SELECT * FROM ordenes_mesa WHERE mesa_id='$mesa_id' AND estado='Abierta'";
$resultado = $conexion->query($sqlOrden);

if($resultado && $resultado->num_rows > 0){
    $orden = $resultado->fetch_assoc();
    $orden_id = $orden['id'];
} else {
    // SOLUCIÓN AL ERROR: Incluimos 'fecha' con la función NOW() de MySQL
    $sqlNuevaOrden = "INSERT INTO ordenes_mesa (mesa_id, fecha, estado) VALUES ('$mesa_id', NOW(), 'Abierta')";
    $conexion->query($sqlNuevaOrden);
    $orden_id = $conexion->insert_id;

    // Cambiamos el estado de la mesa a Ocupada
    $conexion->query("UPDATE mesas SET estado='Ocupada' WHERE id='$mesa_id'");
}

// 2. OBTENER INFORMACIÓN Y PRECIO DEL PRODUCTO
$sqlProducto = "SELECT * FROM productos WHERE id='$producto_id'";
$resProducto = $conexion->query($sqlProducto);

if ($resProducto && $producto = $resProducto->fetch_assoc()) {
    $precio = $producto['precio'];
    $subtotal = $precio * $cantidad;
} else {
    echo json_encode(['success' => false, 'error' => "El producto con ID '$producto_id' no existe."]);
    exit();
}

// 3. REGISTRAR EN DETALLE_ORDEN
$sqlDetalle = "INSERT INTO detalle_orden (orden_id, producto_id, cantidad, precio, subtotal, estado_kitchen) 
               VALUES ('$orden_id', '$producto_id', '$cantidad', '$precio', '$subtotal', 'Pendiente')";

if($conexion->query($sqlDetalle)) {
    
    // 🔥 ¡NUEVO CAMBIO!: Descontamos automáticamente la cantidad añadida del stock del producto
    $sqlStock = "UPDATE productos SET stock = stock - $cantidad WHERE id = '$producto_id'";
    $conexion->query($sqlStock);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al insertar detalle: ' . $conexion->error]);
}
exit();