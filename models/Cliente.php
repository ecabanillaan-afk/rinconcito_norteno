<?php

require_once("Conexion.php");

class Cliente{

    public static function listar(){
        $conexion = Conexion::conectar();
        $sql = "SELECT * FROM clientes";
        return $conexion->query($sql);
    }

    // NUEVO MÉTODO: Agrega esta función para buscar un cliente específico
    public static function buscarPorId($id){
        $conexion = Conexion::conectar();
        
        // Usamos una consulta preparada para proteger la base de datos de inyecciones SQL
        $sql = "SELECT * FROM clientes WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        
        // Vinculamos el parámetro ID como un entero ("i")
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Retornamos el resultado para que el fetch_assoc() en editar.php funcione correctamente
        return $stmt->get_result();
    }
    public static function actualizar($id, $nombres, $telefono, $direccion){
    $conexion = Conexion::conectar();
    
    $sql = "UPDATE clientes SET nombres = ?, telefono = ?, direccion = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    
    // "sssi" significa: string, string, string, e integer (id)
    $stmt->bind_param("sssi", $nombres, $telefono, $direccion, $id);
    
    return $stmt->execute();
}

}

?>