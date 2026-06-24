<?php

session_start();

require_once("../../models/Conexion.php");

$conexion = Conexion::conectar();

$mesas = $conexion->query("SELECT * FROM mesas");

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mesas</title>    

<link rel="stylesheet" href="../css/mesas.css">

</head>
<body>

<div class="mesas-topbar">
    <!-- Botón a la izquierda -->
    <a href="../dashboard/index.php" class="btn-regresar">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    
    <!-- Título en el centro -->
    <h1>🍽️ Gestión de Mesas</h1>
    
    <!-- Bloque invisible a la derecha para equilibrar el centrado del título -->
    <div class="espaciador-derecho"></div>
</div>

<div class="contenedor">

<?php while($mesa = $mesas->fetch_assoc()){ ?>

<div class="mesa <?php echo ($mesa['estado'] == 'Ocupada') ? 'ocupada' : 'libre'; ?>">

    <a href="orden.php?id=<?php echo $mesa['id']; ?>">

        <h2>Mesa <?php echo $mesa['numero']; ?></h2>

        <p><?php echo $mesa['estado']; ?></p>

    </a>

</div>

<?php } ?>

</div>
</div>

<script>
function verificarPedidosListos() {
    // Petición directa a la raíz de tu proyecto local
    fetch('/rinconcito_norteno/controllers/VerificarNotificaciones.php')
        .then(response => response.json())
        .then(data => {
            if (data.hay_notificacion) {
                // Alerta en pantalla usando el número real de la mesa
                alert(`¡Pedido de la Mesa ${data.mesa_num_o ?? data.mesa_numero} listo!: ${data.producto}`);
                
                // Recarga la vista de las mesas para actualizar colores si es necesario
                location.reload();
            }
        })
        .catch(error => console.error("Error en notificaciones:", error));
}

// Revisa la cocina en segundo plano cada 4 segundos
setInterval(verificarPedidosListos, 4000);
</script>

</body>
</html>
</body>
</html>