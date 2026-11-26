<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexion.php";

$metodo = $_SERVER["REQUEST_METHOD"];

if ($metodo === "GET") {

    $consulta = $conexion->query("SELECT * FROM equipos ORDER BY id DESC");

    $equipos = [];

    while ($fila = $consulta->fetch_assoc()) {
        $equipos[] = $fila;
    }

    echo json_encode($equipos);
    exit;
}

if ($metodo === "POST") {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!$datos) {
        echo json_encode(["error" => "Datos inválidos"]);
        exit;
    }

    $nombre = $datos["nombre"] ?? "";
    $categoria = $datos["categoria"] ?? "";
    $marca = $datos["marca"] ?? "";
    $modelo = $datos["modelo"] ?? "";
    $numero_serie = $datos["numero_serie"] ?? "";
    $estado = $datos["estado"] ?? "Disponible";
    $ubicacion = $datos["ubicacion"] ?? "";
    $descripcion = $datos["descripcion"] ?? "";

    $sql = "INSERT INTO equipos 
    (nombre, categoria, marca, modelo, numero_serie, estado, ubicacion, descripcion)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "ssssssss",
        $nombre,
        $categoria,
        $marca,
        $modelo,
        $numero_serie,
        $estado,
        $ubicacion,
        $descripcion
    );

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Equipo registrado correctamente"]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }

    exit;
}

if ($metodo === "PUT") {

    $datos = json_decode(file_get_contents("php://input"), true);

    $id = $datos["id"] ?? 0;
    $nombre = $datos["nombre"] ?? "";
    $categoria = $datos["categoria"] ?? "";
    $marca = $datos["marca"] ?? "";
    $modelo = $datos["modelo"] ?? "";
    $numero_serie = $datos["numero_serie"] ?? "";
    $estado = $datos["estado"] ?? "Disponible";
    $ubicacion = $datos["ubicacion"] ?? "";
    $descripcion = $datos["descripcion"] ?? "";

    $sql = "UPDATE equipos SET 
    nombre=?, categoria=?, marca=?, modelo=?, numero_serie=?, estado=?, ubicacion=?, descripcion=?
    WHERE id=?";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "ssssssssi",
        $nombre,
        $categoria,
        $marca,
        $modelo,
        $numero_serie,
        $estado,
        $ubicacion,
        $descripcion,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Equipo actualizado correctamente"]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }

    exit;
}

if ($metodo === "DELETE") {

    $datos = json_decode(file_get_contents("php://input"), true);
    $id = $datos["id"] ?? 0;

    $stmt = $conexion->prepare("DELETE FROM equipos WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Equipo eliminado correctamente"]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }

    exit;
}

echo json_encode(["error" => "Método no permitido"]);

?>