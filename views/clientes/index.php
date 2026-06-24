    <?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        header("Location: ../login/index.php");
        exit();
    }

    require_once("../../models/Cliente.php");

    $clientes = Cliente::listar();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Clientes</title>
        <!-- Agregamos el truco de versión para que el navegador actualice el diseño de inmediato -->
        <link rel="stylesheet" href="../css/clientes.css?v=1.2">
    </head>
    <body>

    <!-- CONTENEDOR DE LA CABECERA (Título + Botón Regresar) -->
    <div class="header-lista">
        <h1>Lista de Clientes</h1>
        <a href="../dashboard/index.php" class="btn-regresar">Regresar</a>
    </div>

    <a href="registrar.php">Nuevo Cliente</a>

    <br><br>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombres</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>

    <?php while($fila = $clientes->fetch_assoc()){ ?>
    <tr>
        <td><?php echo $fila['id']; ?></td>
        <td><?php echo $fila['nombres']; ?></td>
        <td><?php echo $fila['telefono']; ?></td>
        <td><?php echo $fila['direccion']; ?></td>
        <td>
            <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a>
            <a href="../../controllers/EliminarCliente.php?id=<?php echo $fila['id']; ?>" 
               onclick="return confirm('¿Estás seguro de que deseas eliminar a este cliente?');">Eliminar</a>
        </td>
    </tr>
    <?php } ?>
    </table>

    </body>
    </html>