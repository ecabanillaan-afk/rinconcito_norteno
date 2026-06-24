<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Personal - Rinconcito Norteño</title>
    <link rel="stylesheet" href="../css/usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="contenedor-registro">
    <div class="card-registro">
        <div class="logo-seccion">
            <img src="../css/img/cevi.png" alt="Logo" class="logo-personal" onerror="this.style.display='none';">
            <h2>Registro de Personal</h2>
            <p>De alta a un nuevo miembro del equipo</p>
        </div>

        <form id="formRegistrarUsuario">
            <div class="grupo-input">
                <label><i class="fas fa-user-tag"></i> Nombre Completo</label>
                <input type="text" name="nombre" placeholder="Ej. Juan Pérez" required>
            </div>

            <div class="grupo-input">
                <label><i class="fas fa-user"></i> Nombre de Usuario (Login)</label>
                <input type="text" name="usuario" placeholder="Ej. jperez" required>
            </div>

            <div class="grupo-input">
                <label><i class="fas fa-lock"></i> Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="grupo-input">
                <label><i class="fas fa-briefcase"></i> Rol / Puesto</label>
                <select name="rol" required>
                    <option value="" disabled selected>Seleccione un rol...</option>
                    <option value="Mesero">🍽️ Mesero / Atendimiento</option>
                    <option value="Chef">👨‍🍳 Chef / Cocina</option>
                </select>
            </div>

            <button type="submit" class="btn-registrar">
                <i class="fas fa-user-plus"></i> Registrar e Ingresar
            </button>
        </form>

        <div class="pie-registro">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Volver al Login</a>
        </div>
    </div>
</div>

<script>
document.getElementById('formRegistrarUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../../controllers/RegistrarUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('¡Usuario registrado con éxito!');
            window.location.href = 'index.php'; // Redirige al login para probar
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(err => console.error("Error:", err));
});
</script>
</body>
</html>