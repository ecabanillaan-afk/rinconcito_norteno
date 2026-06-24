<?php
session_start();
require_once "../../models/Conexion.php";
$conexion = Conexion::conectar();

// Obtener clientes para el select
$clientes = $conexion->query("SELECT * FROM clientes");
// Obtener productos para el select
$productos = $conexion->query("SELECT * FROM productos WHERE estado='Activo'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🛵 Nuevo Pedido Delivery</title>
    <link rel="stylesheet" href="../css/mesas.css"> 
    <link rel="stylesheet" href="../css/pedidos.css"> </head>
<body>

<div class="card-pedido">
    
    <div class="header-card-pedido">
        <h2>🛵 Registrar Pedido Delivery / Especial</h2>
        <a href="index.php" class="btn-cancelar-pedido">🔙 Volver al Panel</a>
    </div>
    
    <hr style="border-color: #2d2d35; margin-bottom: 20px;">

    <form id="formNuevoPedido" action="../../controllers/GuardarPedidoDelivery.php" method="POST" onsubmit="return validarAntesDeEnviar(event)">

<form id="formNuevoPedido" action="../../controllers/GuardarPedidoDelivery.php" method="POST" onsubmit="return validarAntesDeEnviar(event)">        
        <div class="form-group">
            <label for="cliente_id">Seleccionar Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-control" onchange="mostrarDireccionCliente()" required>
                <option value="">-- Seleccione un Cliente --</option>
                <?php while($c = $clientes->fetch_assoc()) { ?>
                    <option value="<?php echo $c['id']; ?>" data-direccion="<?php echo $c['direccion']; ?>">
                        <?php echo $c['nombres']; ?> (<?php echo $c['telefono']; ?>)
                    </option>
                <?php } ?>
            </select>
            
            <div id="info_direccion_cliente" style="margin-top: 8px; font-size: 14px; color: #ff9f43; font-style: italic; display: none;">
                📍 Dirección de entrega: <span id="txt_direccion_cliente" style="color: #fff; font-weight: bold;"></span>
            </div>
        </div>

        <div class="card" style="background: #15151b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <label style="color: #ff9f43; font-weight: bold; display:block; margin-bottom:10px;">Agregar Platos / Bebidas</label>
            <div class="grid-2">
                <select id="select_producto" class="form-control">
                    <option value="">-- Elegir Arroz con mariscos, gaseosa, etc --</option>
                    <?php while($p = $productos->fetch_assoc()) { ?>
                        <option value="<?php echo $p['id']; ?>" data-precio="<?php echo $p['precio']; ?>">
                            <?php echo $p['nombre']; ?> - S/ <?php echo $p['precio']; ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="number" id="input_cantidad" class="form-control" value="1" min="1" placeholder="Cantidad">
            </div>
            <button type="button" class="btn-agregar-item" onclick="agregarProductoLista()">+ Agregar al Pedido</button>
        </div>

        <table class="tabla-resumen" id="tablaItems">
            <thead>
                <tr style="color: #ff9f43;">
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cant.</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="grid-2" style="margin-top: 25px;">
            <div class="form-group">
                <label for="costo_delivery">Zona de Delivery</label>
                <select name="costo_delivery" id="costo_delivery" class="form-control" onchange="calcularGranTotal()">
                    <option value="3.00">Cerca (Urbano) - S/ 3.00</option>
                    <option value="5.00">Lejos (Periferia) - S/ 5.00</option>
                    <option value="0.00">Sin Delivery (Recojo en Local) - S/ 0.00</option>
                </select>
            </div>

            <div class="form-group">
                <label for="metodo_pago">Método de Pago</label>
                <select name="metodo_pago" id="metodo_pago" class="form-control">
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Yape">📱 Yape / Plin</option>
                    <option value="Tarjeta">💳 Tarjeta Crédito/Débito</option>
                </select>
            </div>
        </div>

        <div class="box-totales">
            <p>Subtotal Platos: S/ <span id="txt_subtotal">0.00</span></p>
            <p>Delivery: S/ <span id="txt_delivery">3.00</span></p>
            <p class="total-destacado">Total General: S/ <span id="txt_total">0.00</span></p>
        </div>

        <div id="hidden_inputs_container"></div>

        <button type="submit" class="btn-guardar">🚀 Registrar y Enviar a Cocina</button>
    </form>
</div>

<script>
let itemsPedido = [];

function agregarProductoLista() {
    const select = document.getElementById('select_producto');
    const cantidadInput = document.getElementById('input_cantidad');
    
    if(!select.value) return alert("Selecciona un producto");
    
    const productoId = select.value;
    const nombre = select.options[select.selectedIndex].text.split(' - ')[0];
    const precio = parseFloat(select.options[select.selectedIndex].getAttribute('data-precio'));
    const cantidad = parseInt(cantidadInput.value);
    
    const existe = itemsPedido.find(item => item.id === productoId);
    if(existe) {
        existe.cantidad += cantidad;
    } else {
        itemsPedido.push({ id: productoId, nombre, precio, cantidad });
    }
    
    renderizarTabla();
}

function removerItem(id) {
    itemsPedido = itemsPedido.filter(item => item.id !== id);
    renderizarTabla();
}

function renderizarTabla() {
    const tbody = document.querySelector('#tablaItems tbody');
    const container = document.getElementById('hidden_inputs_container');
    tbody.innerHTML = '';
    container.innerHTML = '';
    
    let subtotalPlatos = 0;
    
    itemsPedido.forEach((item, index) => {
        const sub = item.precio * item.cantidad;
        subtotalPlatos += sub;
        
        tbody.innerHTML += `
            <tr>
                <td>${item.nombre}</td>
                <td>S/ ${item.precio.toFixed(2)}</td>
                <td>${item.cantidad}</td>
                <td>S/ ${sub.toFixed(2)}</td>
                <td><button type="button" style="background:#ea5455; color:white; border:none; border-radius:4px; cursor:pointer;" onclick="removerItem('${item.id}')">❌</button></td>
            </tr>
        `;
        
        container.innerHTML += `
            <input type="hidden" name="productos[${index}][id]" value="${item.id}">
            <input type="hidden" name="productos[${index}][cantidad]" value="${item.cantidad}">
            <input type="hidden" name="productos[${index}][precio]" value="${item.precio}">
        `;
    });
    
    document.getElementById('txt_subtotal').innerText = subtotalPlatos.toFixed(2);
    calcularGranTotal();
}

function calcularGranTotal() {
    const subtotal = parseFloat(document.getElementById('txt_subtotal').innerText) || 0;
    const delivery = parseFloat(document.getElementById('costo_delivery').value);
    
    document.getElementById('txt_delivery').innerText = delivery.toFixed(2);
    document.getElementById('txt_total').innerText = (subtotal + delivery).toFixed(2);
}

function mostrarDireccionCliente() {
    const select = document.getElementById('cliente_id');
    const container = document.getElementById('info_direccion_cliente');
    const txtDireccion = document.getElementById('txt_direccion_cliente');
    
    if(!select.value) {
        container.style.display = 'none';
        return;
    }
    
    const direccion = select.options[select.selectedIndex].getAttribute('data-direccion');
    
    if(direccion && direccion.trim() !== '') {
        txtDireccion.innerText = direccion;
    } else {
        txtDireccion.innerText = "No registra dirección (Se enviará como: Recojo en local)";
    }
    
    container.style.display = 'block';
}
function validarAntesDeEnviar(event) {
    const cliente = document.getElementById('cliente_id').value;    
    
    // 1. Validar que se haya seleccionado un cliente
    if (!cliente) {
        alert("⚠️ Por favor, selecciona un cliente antes de continuar.");
        event.preventDefault(); // Detiene el envío
        return false;
    }
    
    // 2. Validar que la lista de productos no esté vacía
    if (itemsPedido.length === 0) {
        alert("⚠️ El pedido está vacío. Debes elegir un producto y hacer clic en '+ Agregar al Pedido'.");
        event.preventDefault(); // Detiene el envío
        return false;
    }
    
    // Si todo está bien, permite que el formulario viaje al controlador
    return true;
}
</script>
</body>
</html>