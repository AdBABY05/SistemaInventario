<?php

require_once __DIR__ . "/sesion.php";

if (!isset($_SESSION["usuario_id"])) {

    $ruta = $_SERVER["SCRIPT_NAME"] ?? "";

    if (strpos($ruta, "/backend/") !== false) {

        http_response_code(401);
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode([
            "error" => "Sesión no autorizada."
        ]);

        exit;

    }

    header("Location: /SistemaInventario/login.php");
    exit;

}

?>