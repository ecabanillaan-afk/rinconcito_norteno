<?php
session_start();

// FORZAR A MOSTRAR ERRORES (Esto quitará el Error 500 genérico y te dirá la línea exacta)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../../models/Conexion.php");
$conexion = Conexion::conectar();

// Verificamos si la columna estado_cocina existe en detalle_orden, si no, la creamos automáticamente
$conexion->query("ALTER TABLE detalle_orden ADD COLUMN IF NOT EXISTS estado_cocina VARCHAR(50) DEFAULT 'Pendiente'");

// Consulta corregida y optimizada
// Consulta corregida: trae todo lo que NO sea de la categoría 'Bebidas'
$sqlOrdenes = "SELECT DISTINCT o.id AS orden_id, o.mesa_id, o.fecha 
               FROM ordenes_mesa o
               INNER JOIN detalle_orden d ON o.id = d.orden_id
               INNER JOIN productos p ON d.producto_id = p.id
               WHERE o.estado = 'Abierta' 
               AND p.categoria != 'Bebidas' 
               AND (d.estado_cocina = 'Pendiente' OR d.estado_cocina IS NULL OR d.estado_cocina = '')
               ORDER BY o.fecha ASC";

$ordenes = $conexion->query($sqlOrdenes);

// Si la consulta falla, esto te dirá exactamente por qué en la pantalla
if (!$ordenes) {
    die("Error en la consulta SQL: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>👨‍🍳 Pedidos del Chef</title>
    <link rel="stylesheet" href="../css/chef.css">
    <meta http-equiv="refresh" content="15">
</head>
<body>
<div class="chef-topbar">
    <div class="espaciador-izquierdo"></div>
    
    <h1>👨‍🍳 Pedidos en Cocina (Solo Comidas)</h1>
    
    <a href="../dashboard/index.php" class="btn-regresar">
        Volver 📋
    </a>
</div>

<div class="tablero-cocina" style="display: flex; flex-wrap: wrap; gap: 20px; padding: 20px; justify-content: flex-start; align-items: flex-start;">

    <?php 
    if($ordenes && $ordenes->num_rows > 0) {
        while($orden = $ordenes->fetch_assoc()) { 
            $orden_id = $orden['orden_id'];
            
            // Traemos solo los platos que NO corresponden a la categoría 'Bebidas' de esta mesa
            $sqlPlatos = "SELECT d.cantidad, p.nombre 
                          FROM detalle_orden d
                          INNER JOIN productos p ON d.producto_id = p.id
                          WHERE d.orden_id = '$orden_id' 
                          AND p.categoria != 'Bebidas'
                          AND (d.estado_cocina IS NULL OR d.estado_cocina = 'Pendiente' OR d.estado_cocina = '')";
            $platos = $conexion->query($sqlPlatos);
    ?>
            <div class="ticket-orden" id="ticket-<?php echo $orden_id; ?>" style="min-width: 280px; max-width: 350px; flex: 1 1 280px; box-sizing: border-box;">
                <div>
                    <div class="ticket-header">
                        <span class="ticket-mesa">Mesa #<?php echo $orden['mesa_id']; ?></span>
                        <span class="ticket-tiempo">Pendiente</span>
                    </div>

                    <ul class="lista-platos">
                        <?php while($plato = $platos->fetch_assoc()) { ?>
                            <li class="item-plato">
                                <span class="plato-cantidad">x<?php echo $plato['cantidad']; ?></span>
                                <span class="plato-nombre"><?php echo $plato['nombre']; ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <button class="btn-despachar" onclick="despacharOrden(<?php echo $orden_id; ?>)">
                    ✔ ¡Platos Listos!
                </button>
            </div>
    <?php 
        } 
    } else { 
    ?>
        <div style="width: 100%; text-align: center; padding: 50px; color: #7f8c8d; font-size: 1.5em;">
            🎉 ¡Todo preparado! No hay comidas pendientes.
        </div>
    <?php } ?>

</div>

<script>
function despacharOrden(ordenId) {
    if(confirm('¿Confirmas que terminaste de preparar todos los platos de esta mesa?')) {
        const formData = new FormData();
        formData.append('orden_id', ordenId);

        fetch('../../controllers/DespacharOrden.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Efecto visual rápido de desaparición antes de recargar
                const ticket = document.getElementById('ticket-' + ordenId);
                ticket.style.opacity = '0';
                setTimeout(() => { location.reload(); }, 300);
            } else {
                alert('Hubo un problema al despachar el pedido.');
            }
        });
    }
}
</script>

</body>
</html>