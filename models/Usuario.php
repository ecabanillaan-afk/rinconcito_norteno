<?php

require_once("Conexion.php");

class Usuario {

    public static function login($usuario, $password){

        $conexion = Conexion::conectar();

        $sql = "SELECT * FROM usuarios
                WHERE usuario='$usuario'
                AND password='$password'";

        return $conexion->query($sql);
    }
}