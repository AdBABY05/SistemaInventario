
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "seguridad/sesion.php";

if (isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

require_once "backend/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($usuario === "" || $password === "") {

        $mensaje = "Complete todos los campos.";

    } else {

        $stmt = $conexion->prepare(
            "SELECT id, usuario, password FROM usuarios WHERE usuario = ? LIMIT 1"
        );

        $stmt->bind_param("s", $usuario);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $cuenta = $resultado->fetch_assoc();

        if ($cuenta && password_verify($password, $cuenta["password"])) {

            session_regenerate_id(true);

            $_SESSION["usuario_id"] = $cuenta["id"];
            $_SESSION["usuario"] = $cuenta["usuario"];

            header("Location: index.php");
            exit;

        } else {

            $mensaje = "Usuario o contraseña incorrectos.";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inicio de sesión - SistemaInventario</title>

    <link rel="stylesheet" href="frontend/css/estilos.css">

    <style>

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-contenedor {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.10);
        }

        .login-contenedor h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .login-descripcion {
            text-align: center;
            margin-bottom: 25px;
            color: #64748b;
        }

        .login-contenedor form {
            display: flex;
            flex-direction: column;
        }

        .login-contenedor input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
        }

        .login-contenedor button {
            width: 100%;
            margin-top: 10px;
        }

        .login-error {
            color: #b91c1c;
            text-align: center;
            margin-bottom: 15px;
        }

    </style>

</head>
<body>

    <div class="login-contenedor">

        <h1>INVENTARIO</h1>

        <p class="login-descripcion">
            Sistema de gestión de equipos tecnológicos
        </p>

        <?php if ($mensaje !== ""): ?>

            <p class="login-error">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label for="usuario">Usuario</label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                maxlength="50"
                autocomplete="username"
                required
            >

            <label for="password">Contraseña</label>

            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit" class="boton">
                Iniciar sesión
            </button>

        </form>

    </div>

</body>
</html>
