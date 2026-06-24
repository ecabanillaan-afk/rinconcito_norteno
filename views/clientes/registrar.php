<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente - Rinconcito Norteño</title>
    <link rel="stylesheet" href="../css/clientes.css?v=1.1">
</head>
<body class="body-registro"> <div class="contenedor-registro">
    <div class="tarjeta-registro">
        
        <div class="encabezado-registro">
            <h2>Rinconcito Norteño</h2>
            <p>Registrar Nuevo Cliente</p>
        </div>

        <form action="../../controllers/ClienteController.php" method="POST" class="formulario-registro">
            
            <div class="grupo-input">
                <label for="nombres">Nombres y Apellidos</label>
                <input type="text" id="nombres" name="nombres" placeholder="Ej. Lisbet" required autocomplete="off">
            </div>

            <div class="grupo-input">
                <label for="telefono">Teléfono / Celular</label>
                <input type="text" id="telefono" name="telefono" placeholder="Ej. 923242332" required autocomplete="off">
            </div>

            <div class="grupo-input">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" placeholder="Ej. Av. Central 123" required autocomplete="off">
            </div>

            <div class="acciones-form">
                <button type="submit" class="btn-guardar">Guardar Cliente</button>
                <a href="index.php" class="btn-cancelar">Volver a la Lista</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>