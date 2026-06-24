<?php
session_start();

// Validar que el usuario esté logueado
if(!isset($_SESSION['usuario'])){
    header("Location: ../views/login/index.php");
    exit();
}

// Verificar que lleguen los datos del formulario
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    
    require_once("../models/Cliente.php");

    $id = $_POST['id'];
    $nombres = $_POST['nombres'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Aquí llamaremos al método del modelo que actualizará los datos
    // (Este método lo crearemos en el siguiente paso)
    $actualizado = Cliente::actualizar($id, $nombres, $telefono, $direccion);

    if($actualizado) {
        // Si se editó con éxito, regresa a la lista de clientes
        header("Location: ../views/clientes/index.php");
    } else {
        echo "Error al actualizar el cliente.";
    }
} else {
    header("Location: ../views/clientes/index.php");
}
?>