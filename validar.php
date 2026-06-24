<?php
session_start();

require_once("models/Conexion.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$conexion = Conexion::conectar();

// Regresamos a la consulta directa original
$sql = "SELECT * FROM usuarios
        WHERE usuario='$usuario'
        AND password='$password'";

$resultado = $conexion->query($sql);

if($resultado && $resultado->num_rows > 0){
    $fila = $resultado->fetch_assoc();

    $_SESSION['usuario'] = $usuario;
    $_SESSION['rol'] = $fila['rol']; // Mantenemos el rol por si tu sistema lo usa

    header("Location: views/dashboard/index.php");
    exit();
} else {
    echo "Usuario o contraseña incorrectos";
}