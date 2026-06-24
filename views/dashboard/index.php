<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../login/index.php");
    exit();
}

require_once("../../models/Conexion.php");

$conexion = Conexion::conectar();

$totalClientes = $conexion->query(
    "SELECT * FROM clientes"
)->num_rows;

$totalProductos = $conexion->query(
    "SELECT * FROM productos"
)->num_rows;

$totalPedidos = $conexion->query(
    "SELECT * FROM pedidos"
)->num_rows;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rinconcito Norteño</title>

    <link rel="stylesheet" href="../css/dashboard.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body>

<div class="contenedor">

    <div class="sidebar">

        <h2>🐟 Rinconcito Norteño</h2>

        <ul>

            <li>
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li>
                 <a href="../mesas/index.php">
                    <i class="fas fa-chair"></i>
                     Mesas
                 </a>
            </li>
            
            <li>
                <a href="../clientes/index.php">
                    <i class="fas fa-users"></i>
                    Clientes Delivery
                </a>
            </li>

            <li>
                <a href="../productos/index.php">
                    <i class="fas fa-utensils"></i>
                    Productos
                </a>
            </li>

            <li>
                <a href="../pedidos/index.php">
                    <i class="fas fa-cart-shopping"></i>
                    Pedidos
                </a>
            </li>

           <li>
                    <a href="../pedidos/chef.php">
                      👨‍🍳 Chef Pedidos
                  </a>
            </li>

            <li>
<li>
    <a href="../reportes/index.php">
        <i class="fas fa-chart-pie"></i> Reportes
    </a>
</li>

            <li>
                 <a href="../ventas/index.php">
                    <i class="fas fa-chart-line"></i>
                    🧾 Ventas
                </a>
            </li>
<li>
    <a href="../../logout.php">
        <i class="fas fa-right-from-bracket"></i>
        Cerrar Sesión
    </a>
</li>

        </ul>

    </div>

    <div class="contenido">

        <div class="topbar">

            <h1>
                Bienvenido,
                <?php echo $_SESSION['usuario']; ?>
            </h1>

        </div>

        <div class="cards">

            <div class="card">
                <h3>Clientes</h3>
                <p><?php echo $totalClientes; ?></p>
            </div>

            <div class="card">
                <h3>Productos</h3>
                <p><?php echo $totalProductos; ?></p>
            </div>

            <div class="card">
                <h3>Pedidos</h3>
                <p><?php echo $totalPedidos; ?></p>
            </div>

<div class="card">
    <h3>Ventas Totales</h3>
    <p>
        S/. <?php 
        // Sumamos los delivery + las mesas cerradas de la base de datos
        $ingresoDelivery = $conexion->query("SELECT SUM(total) as t FROM pedidos")->fetch_assoc()['t'] ?? 0;
        $ingresoMesas = $conexion->query("SELECT SUM(subtotal) as t FROM detalle_orden WHERE orden_id IN (SELECT id FROM ordenes_mesa WHERE estado='Cerrada')")->fetch_assoc()['t'] ?? 0;
        echo number_format($ingresoDelivery + $ingresoMesas, 2);
        ?>
    </p>
</div>

        </div>

        <div class="panel carrusel-container">
            <div class="carrusel-track">
                
                <div class="carrusel-slide">
                    <img src="../css/img/slide1.png" alt="Ceviche Especial">
                    <div class="carrusel-caption">¡Bienvenidos a Rinconcito Norteño!</div>
                </div>
                
                <div class="carrusel-slide">
                    <img src="../css/img/slide2.png" alt="Chicharrón de Pescado">
                    <div class="carrusel-caption">Frescura, Sabor y Tradición Marina</div>
                </div>
                
                <div class="carrusel-slide">
                    <img src="../css/img/slide3.png" alt="Ronda Marina">
                    <div class="carrusel-caption">Gestión Rápida de Pedidos y Mesas</div>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
let currentIndex = 0;
const track = document.querySelector('.carrusel-track');
const slides = document.querySelectorAll('.carrusel-slide');

function moverCarrusel() {
    currentIndex++;
    if (currentIndex >= slides.length) {
        currentIndex = 0; 
    }
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
}

setInterval(moverCarrusel, 3500);
</script>

</body>
</html>