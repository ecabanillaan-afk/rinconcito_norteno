    <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../models/Conexion.php";
$conexion = Conexion::conectar();

$accion = $_GET['accion'] ?? null;

// 1. ACCIÓN: CREAR PRODUCTO
if ($accion === 'crear') {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    
    // Procesar carga de archivo imagen
    $nombre_imagen = ""; // Por defecto si no suben nada
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio_destino = "../views/css/img/";
        
        // Generar un nombre único basado en tiempo para evitar duplicados
        $nombre_imagen = time() . "_" . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio_destino . $nombre_imagen);
    }

    $sql = "INSERT INTO productos (nombre, categoria, precio, stock, estado, imagen) 
            VALUES ('$nombre', '$categoria', '$precio', '$stock', 'Activo', '$nombre_imagen')";
    $conexion->query($sql);
}

// 2. ACCIÓN: ACTUALIZAR PRODUCTO
if ($accion === 'actualizar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Si el usuario sube un nuevo archivo gráfico se reemplaza
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio_destino = "../views/css/img/";
        $nombre_imagen = time() . "_" . basename($_FILES['imagen']['name']);
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio_destino . $nombre_imagen)) {
            $conexion->query("UPDATE productos SET imagen='$nombre_imagen' WHERE id='$id'");
        }
    }

    $sql = "UPDATE productos SET nombre='$nombre', categoria='$categoria', precio='$precio', stock='$stock' WHERE id='$id'";
    $conexion->query($sql);
}

// 3. ACCIÓN: ELIMINAR PRODUCTO
if ($accion === 'eliminar') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $sql = "DELETE FROM productos WHERE id='$id'";
        $conexion->query($sql);
    }
}

// Redirección limpia al catálogo principal
header("Location: ../views/productos/index.php");
exit();