<?php

class Conexion{

    public static function conectar(){

        $host = "localhost";
        $usuario = "root";
        $password = "";
        $bd = "rinconcito_norteno";

        $conexion = new mysqli(
            $host,
            $usuario,
            $password,
            $bd
        );

        return $conexion;
    }
}
?>