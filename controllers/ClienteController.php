<?php

require_once("../models/Conexion.php");

$nombres = $_POST['nombres'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];

$conexion = Conexion::conectar();

$sql = "INSERT INTO clientes
        (nombres,telefono,direccion)
        VALUES
        ('$nombres','$telefono','$direccion')";

$conexion->query($sql);

header("Location: ../views/clientes/index.php");
exit();

?>