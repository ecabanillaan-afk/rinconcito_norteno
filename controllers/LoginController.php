<?php

session_start();

require_once("../models/Usuario.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$resultado = Usuario::login(
    $usuario,
    $password
);

if($resultado->num_rows > 0){

    $_SESSION['usuario'] = $usuario;

    header(
        "Location: ../views/dashboard/index.php"
    );

}else{

    echo "Datos incorrectos";
}