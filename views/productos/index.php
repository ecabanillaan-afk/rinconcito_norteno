<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../login/index.php");
    exit();
}

require_once("../../models/Producto.php");
$productos = Producto::listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>
<body>

<div class="cabecera">
    <div class="cabecera-flex">
        <input type="text" id="inputBusqueda" placeholder="Buscar producto...">
        <h1>📦 Listado de Productos</h1>
        <button class="btn-abrir-nuevo" onclick="abrirModalAgregar()">➕ Nuevo Producto</button>
    </div>
</div>

<div class="filtros">
    <button class="btn-filtro active" data-categoria="Todos">Todos</button>
    <button class="btn-filtro" data-categoria="Bebidas">Bebidas</button>
    <button class="btn-filtro" data-categoria="Pescados y Mariscos">Pescados y Mariscos</button>
</div>

<div class="contenedor-productos">
<?php while($fila = $productos->fetch_assoc()){ ?>
    <div class="card" data-categoria="<?php echo $fila['categoria']; ?>">
        <img src="../css/img/<?php echo !empty($fila['imagen']) ? $fila['imagen'] : 'default.png'; ?>" alt="">
        <h2><?php echo $fila['nombre']; ?></h2>
        <p>Stock: <?php echo $fila['stock']; ?></p>
        <h3>S/ <?php echo number_format($fila['precio'],2); ?></h3>

        <div class="acciones">
            <button class="btn-accion-js" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($fila)); ?>)">✏️</button>
            
            <a href="#" class="btn-accion-js" onclick="confirmarEliminar(event, <?php echo $fila['id']; ?>, '<?php echo htmlspecialchars($fila['nombre'], ENT_QUOTES); ?>')">🗑️</a>
        </div>
    </div>
<?php } ?>
</div>

<div id="modalAgregar" class="modal-producto">
    <div class="modal-content">
        <button class="btn-prod-close" onclick="cerrarModalAgregar()">X</button>
        <h2 style="margin:0 0 15px 0;">➕ Registrar Producto</h2>
        <hr style="border-color: #2d2d35; margin-bottom: 15px;">
        
        <form action="../../controllers/ProductoController.php?accion=crear" method="POST" enctype="multipart/form-data">
            <div class="form-group-prod">
                <label>Nombre del Producto</label>
                <input type="text" name="nombre" class="form-control-prod" required>
            </div>
            <div class="form-group-prod">
                <label>Categoría</label>
                <select name="categoria" class="form-control-prod" required>
                    <option value="Pescados y Mariscos">Pescados y Mariscos</option>
                    <option value="Bebidas">Bebidas</option>
                </select>
            </div>
            <div class="form-group-prod">
                <label>Precio (S/.)</label>
                <input type="number" step="0.01" name="precio" class="form-control-prod" required>
            </div>
            <div class="form-group-prod">
                <label>Stock Inicial</label>
                <input type="number" name="stock" class="form-control-prod" value="0" min="0" required>
            </div>
            <div class="form-group-prod">
                <label>Imagen (.png, .jpg)</label>
                <input type="file" name="imagen" class="form-control-prod" accept="image/*">
            </div>
            <button type="submit" class="btn-prod-submit">💾 Guardar e Insertar</button>
        </form>
    </div>
</div>

<div id="modalEditar" class="modal-producto">
    <div class="modal-content">
        <button class="btn-prod-close" onclick="cerrarModalEditar()">X</button>
        <h2 style="margin:0 0 15px 0;">✏️ Editar Producto</h2>
        <hr style="border-color: #2d2d35; margin-bottom: 15px;">
        
        <form action="../../controllers/ProductoController.php?accion=actualizar" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-group-prod">
                <label>Nombre del Producto</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control-prod" required>
            </div>
            <div class="form-group-prod">
                <label>Categoría</label>
                <select name="categoria" id="edit_categoria" class="form-control-prod" required>
                    <option value="Pescados y Mariscos">Pescados y Mariscos</option>
                    <option value="Bebidas">Bebidas</option>
                </select>
            </div>
            <div class="form-group-prod">
                <label>Precio (S/.)</label>
                <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control-prod" required>
            </div>
            <div class="form-group-prod">
                <label>Stock Actual</label>
                <input type="number" name="stock" id="edit_stock" class="form-control-prod" required>
            </div>
            <div class="form-group-prod">
                <label>Actualizar Imagen (Opcional)</label>
                <input type="file" name="imagen" class="form-control-prod" accept="image/*">
            </div>
            <button type="submit" class="btn-prod-submit" style="background:#28c76f;">🔄 Actualizar Datos</button>
        </form>
    </div>
</div>

<script>
// --- TU LÓGICA ORIGINAL DE BUSQUEDA Y FILTROS ---
let categoriaActual = "Todos";
let textoActual = "";

const inputBusqueda = document.getElementById('inputBusqueda');
const botonesFiltro = document.querySelectorAll('.btn-filtro');
const tarjetas = document.querySelectorAll('.contenedor-productos .card');

function filtrarProductos() {
    tarjetas.forEach(tarjeta => {
        let nombreProducto = tarjeta.querySelector('h2').textContent.toLowerCase();
        let categoriaProducto = tarjeta.getAttribute('data-categoria');
        let coincideCategoria = (categoriaActual === "Todos" || categoriaProducto === categoriaActual);
        let coincideTexto = nombreProducto.includes(textoActual);

        if (coincideCategoria && coincideTexto) {
            tarjeta.style.display = ""; 
        } else {
            tarjeta.style.display = "none";
        }
    });
}

inputBusqueda.addEventListener('keyup', function() {
    textoActual = this.value.toLowerCase();
    filtrarProductos();
});

botonesFiltro.forEach(boton => {
    boton.addEventListener('click', function() {
        botonesFiltro.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        categoriaActual = this.getAttribute('data-categoria');
        filtrarProductos();
    });
});

// --- NUEVA LÓGICA PARA MODALES Y ACCIONES ---
function abrirModalAgregar() { document.getElementById('modalAgregar').style.display = 'block'; }
function cerrarModalAgregar() { document.getElementById('modalAgregar').style.display = 'none'; }

function abrirModalEditar(producto) {
    document.getElementById('edit_id').value = producto.id;
    document.getElementById('edit_nombre').value = producto.nombre;
    document.getElementById('edit_categoria').value = producto.categoria;
    document.getElementById('edit_precio').value = producto.precio;
    document.getElementById('edit_stock').value = producto.stock;
    document.getElementById('modalEditar').style.display = 'block';
}
function cerrarModalEditar() { document.getElementById('modalEditar').style.display = 'none'; }

// Confirmación segura de eliminación
function confirmarEliminar(event, id, nombre) {
    event.preventDefault();
    if(confirm(`⚠️ ¿Estás completamente seguro de que deseas eliminar "${nombre}" del menú?\nEsta acción es irreversible.`)) {
        window.location.href = `../../controllers/ProductoController.php?accion=eliminar&id=${id}`;
    }
}
</script>
</body>
</html>