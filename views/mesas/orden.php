<?php
session_start();
require_once("../../models/Conexion.php");
$conexion = Conexion::conectar();

$mesa_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Obtener productos para el select
$productos = $conexion->query("SELECT * FROM productos");

// OBTENER EL CONSUMO ACTUAL DE LA MESA (Si tiene orden abierta)
$sqlConsumo = "SELECT d.cantidad, p.nombre, d.precio, d.subtotal 
               FROM detalle_orden d
               INNER JOIN ordenes_mesa o ON d.orden_id = o.id
               INNER JOIN productos p ON d.producto_id = p.id
               WHERE o.mesa_id = '$mesa_id' AND o.estado = 'Abierta'";
$consumo = $conexion->query($sqlConsumo);

// Calcular el total de la cuenta
$totalCuenta = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden Mesa <?php echo $mesa_id; ?></title>
    <link rel="stylesheet" href="../css/orden.css">
</head>
<body>

<h1>🍽️ Gestión de Mesa <?php echo $mesa_id; ?></h1>

<div class="contenedor-mesa">

    <div class="col-izquierda">
        <h2>Agregar Producto</h2>
        <form id="formAgregarProducto">
            <input type="hidden" name="mesa_id" value="<?php echo $mesa_id; ?>">

            <label>Producto</label>
            <select name="producto_id">
                <?php while($producto = $productos->fetch_assoc()){ ?>
                    <option value="<?php echo $producto['id']; ?>">
                        <?php echo $producto['nombre']; ?> - S/. <?php echo $producto['precio']; ?>
                    </option>
                <?php } ?>
            </select>

            <label>Cantidad</label>
            <input type="number" name="cantidad" value="1" min="1">

            <button type="submit" class="btn-agregar">
                Agregar Producto
            </button>
        </form>

        <div id="mensajeExito" class="alerta-exito">
            ✅ Producto agregado con éxito
        </div>
    </div>

    <div class="col-derecha">
        <h2>Registro de Pedido</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Cant.</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if($consumo && $consumo->num_rows > 0){ ?>
                    <?php while($item = $consumo->fetch_assoc()){ 
                        $totalCuenta += $item['subtotal'];
                    ?>
                        <tr>
                            <td><?php echo $item['cantidad']; ?></td>
                            <td><?php echo $item['nombre']; ?></td>
                            <td>S/. <?php echo number_format($item['precio'], 2); ?></td>
                            <td>S/. <?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #777;">No hay productos registrados en esta mesa.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="total-seccion">
            Total a Pagar: S/. <?php echo number_format($totalCuenta, 2); ?>
        </div>

        <?php if($totalCuenta > 0){ ?>
            <button id="btnCerrarMesa" class="btn-cerrar">Cerrar Mesa (Liberar)</button>
        <?php } ?>
    </div>

</div>

<script>
// 1. ENVÍO DEL FORMULARIO MEDIANTE AJAX
document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const formData = new FormData(this);

    fetch('../../controllers/AgregarProductoMesa.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const alerta = document.getElementById('mensajeExito');
            alerta.style.display = 'block';

            setTimeout(() => {
                location.reload();
            }, 1200);
        } else {
            alert("Error del servidor: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Ocurrió un error al procesar la solicitud.");
    });
});

// 2. ACCIÓN DEL BOTÓN CERRAR MESA
const btnCerrar = document.getElementById('btnCerrarMesa');
if(btnCerrar) {
    btnCerrar.addEventListener('click', function() {
        if(confirm('¿Estás seguro de que el cliente canceló la cuenta y deseas liberar la mesa?')) {
            const formData = new FormData();
            formData.append('mesa_id', '<?php echo $mesa_id; ?>');

            fetch('../../controllers/CerrarMesa.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('¡Mesa liberada correctamente!');
                    window.location.href = 'index.php';
                } else {
                    alert('Error al cerrar la mesa.');
                }
            });
        }
    });
}
</script>
<script>
// Función que consulta al servidor por platos listos
function verificarPedidosListos() {
    // Apuntamos al controlador que creamos en el Paso 1
    fetch('../../controllers/VerificarNotificaciones.php')
        .then(response => response.json())
        .then(data => {
            if (data.hay_notificacion) {
                // Sonido de alerta opcional (puedes omitirlo si no tienes un audio)
                let audio = new Audio('../../assets/notification.mp3'); 
                audio.play().catch(e => console.log("Audio bloqueado por el navegador"));

                // Mostramos la alerta en pantalla tal como la solicitaste
                alert(`¡Pedido de la Mesa ${data.mesa_id} listo!: ${data.producto}`);
                
                // Opcional: Aquí puedes recargar la tabla de la mesa actual si estás dentro de ella
                if (typeof cargarPedido === 'function') {
                    cargarPedido(); 
                }
            }
        })
        .catch(error => console.error("Error verificando notificaciones:", error));
}

// Configuramos para que pregunte en segundo plano cada 5 segundos (5000 milisegundos)
setInterval(verificarPedidosListos, 5000);
</script>
</body>
</html>