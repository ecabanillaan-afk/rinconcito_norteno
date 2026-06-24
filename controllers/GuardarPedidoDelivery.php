<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

$cliente_id = $_POST['cliente_id'] ?? null;
$costo_delivery = $_POST['costo_delivery'] ?? 0.00;
$metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
$productos = $_POST['productos'] ?? [];

if (!$cliente_id || empty($productos)) {
    die("Error: Faltan datos para procesar el delivery.");
}

// 1. Calcular el total acumulado
$total_platos = 0;
foreach($productos as $p) {
    $total_platos += ($p['precio'] * $p['cantidad']);
}
$total_general = $total_platos + $costo_delivery;

// 2. Insertar en la tabla pedidos
$sqlPedido = "INSERT INTO pedidos (cliente_id, fecha, total, tipo_envio, costo_delivery, metodo_pago, estado_pedido) 
              VALUES ('$cliente_id', NOW(), '$total_general', 'Delivery', '$costo_delivery', '$metodo_pago', 'En proceso')";

if($conexion->query($sqlPedido)) {
    
    // 3. CREAR ORDEN VIRTUAL PARA LA COCINA (Mesa 99 = Delivery)
    $sqlOrdenVirtual = "INSERT INTO ordenes_mesa (mesa_id, fecha, estado) 
                        VALUES (99, NOW(), 'Abierta')";
    
    if($conexion->query($sqlOrdenVirtual)) {
        $orden_virtual_id = $conexion->insert_id; 
        
        // 4. Insertar los platos y DESCONTAR STOCK
        foreach($productos as $p) {
            $p_id = $p['id'];
            $cant = $p['cantidad'];
            $prec = $p['precio'];
            $subt = $prec * $cant;
            
            // Insertar detalle
            $sqlDetalle = "INSERT INTO detalle_orden (orden_id, producto_id, cantidad, precio, subtotal, estado_kitchen, estado_cocina) 
                           VALUES ('$orden_virtual_id', '$p_id', '$cant', '$prec', '$subt', 'Pendiente', 'Pendiente')";
            $conexion->query($sqlDetalle);

            // ¡MAGIA! Descontar del stock de la tabla productos
            $sqlStock = "UPDATE productos SET stock = stock - $cant WHERE id = '$p_id'";
            $conexion->query($sqlStock);
        }
    }
    
    header("Location: ../views/pedidos/index.php");
    exit();
} else {
    echo "Error al registrar el pedido: " . $conexion->error;
}
exit();