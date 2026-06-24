<?php
// 1. Iniciar la sesión existente
session_start();

// 2. Desarmar todas las variables de sesión
$_SESSION = array();

// 3. Destruir la sesión por completo en el servidor
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 4. Redirección limpia y directa a la pantalla de Login
header("Location: views/login/index.php");
exit();
?>