<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../login/index.php");
    exit();
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id_cliente = $_GET['id'];

require_once("../../models/Cliente.php");

$resultado = Cliente::buscarPorId($id_cliente); 

if($resultado && $fila = $resultado->fetch_assoc()){
    $nombres = $fila['nombres'];
    $telefono = $fila['telefono'];
    $direccion = $fila['direccion'];
} else {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="../css/clientes.css?v=1.3">
</head>
<body class="body-editar">

<div class="contenedor-editar">
    <div class="tarjeta-editar">
        
        <div class="encabezado-editar">
            <h2>Editar Cliente</h2>
            <p>Modifica los campos del cliente seleccionado</p>
        </div>

        <form action="../../controllers/EditarCliente.php" method="POST" class="formulario-registro">
            
            <input type="hidden" name="id" value="<?php echo $id_cliente; ?>">

            <div class="grupo-input">
                <label for="nombres">Nombres:</label>
                <input type="text" name="nombres" id="nombres" value="<?php echo $nombres; ?>" required>
            </div>

            <div class="grupo-input">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" value="<?php echo $telefono; ?>" required>
            </div>

            <div class="grupo-input">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" value="<?php echo $direccion; ?>" required>
            </div>

            <div class="acciones-form">
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
                <a href="index.php" class="btn-cancelar">Cancelar y regresar</a>
            </div>
            
        </form>

    </div>
</div>

</body>
</html>