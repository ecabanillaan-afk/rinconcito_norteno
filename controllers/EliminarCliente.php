<?php

require_once("../models/Conexion.php");

$id = $_GET['id'];

$conexion = Conexion::conectar();

$sql = "DELETE FROM clientes WHERE id='$id'";

$conexion->query($sql);

header("Location: ../views/clientes/index.php");
exit();