<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

$nombre = $_POST['nombre'] ?? null;
$usuario = $_POST['usuario'] ?? null;
$password = $_POST['password'] ?? null;
$rol = $_POST['rol'] ?? null;

if (!$nombre || !$usuario || !$password || !$rol) {
    echo json_encode(['success' => false, 'error' => 'Por favor complete todos los campos obligatorios.']);
    exit();
}

// 1. Evitar nombres de usuario duplicados
$usuarioEscapado = $conexion->real_escape_string($usuario);
$checkUser = $conexion->query("SELECT id FROM usuarios WHERE usuario = '$usuarioEscapado'");
if ($checkUser && $checkUser->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'El nombre de usuario ya se encuentra registrado.']);
    exit();
}

// 2. Encriptar la contraseña por seguridad
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$nombreEscapado = $conexion->real_escape_string($nombre);
$rolEscapado = $conexion->real_escape_string($rol);

// 3. Insertar en la tabla usuarios
$sql = "INSERT INTO usuarios (usuario, password, nombre, rol) 
        VALUES ('$usuarioEscapado', '$passwordHash', '$nombreEscapado', '$rolEscapado')";

if ($conexion->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar en la base de datos: ' . $conexion->error]);
}
exit();