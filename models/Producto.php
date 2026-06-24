<?php

require_once("Conexion.php");

class Producto{

    public static function listar(){

        $conexion = Conexion::conectar();

        $sql = "SELECT * FROM productos";

        return $conexion->query($sql);
    }
}
?>