<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="contenedor">

    <div class="izquierda">
        <img src="../css/img/cevi.png" alt="Logo">
        <h2>Sistema Punto de Venta</h2>
        <p>Rinconcito Norteño</p>
        <p>Los Olivos - Lima - Perú</p>
        <p>+51 920295137</p>
    </div>

    <div class="derecha">
        <h1>Inicio de sesión</h1>

        <form action="../../validar.php" method="POST">
            <label>Usuario</label>
            <input type="text" name="usuario" required>

            <label>Contraseña</label>
            <input type="password" name="password" required>

            <button type="submit">
                Iniciar Sesión
            </button>
        </form>

        <div class="opciones-registro" style="margin-top: 25px; text-align: center; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
            <p style="color: #64748b; font-size: 13px; margin: 0 0 12px 0; font-family: sans-serif;">¿Nuevo personal en el restaurante?</p>
            <a href="registro.php" style="display: inline-block; width: 100%; background: #28c76f; color: white; text-decoration: none; padding: 12px; font-size: 14px; font-weight: bold; border-radius: 6px; box-sizing: border-box; transition: background 0.2s; text-align: center; font-family: sans-serif;">
                <i class="fas fa-user-plus"></i> Registrar Mesero / Chef
            </a>
        </div>

    </div>

</div>

</body>
</html>