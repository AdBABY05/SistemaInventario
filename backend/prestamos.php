<?php

require_once "conexion.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$datos = json_decode(file_get_contents("php://input"), true);

try {

    if ($metodo === "GET") {

        $sql = "SELECT 
                    p.id,
                    p.equipo_id,
                    e.nombre AS equipo,
                    e.categoria,
                    e.marca,
                    e.modelo,
                    p.nombre_responsable,
                    p.fecha_prestamo,
                    p.fecha_prevista,
                    p.fecha_devolucion_real,
                    p.estado,
                    p.observaciones
                FROM prestamos p
                INNER JOIN equipos e ON p.equipo_id = e.id
                ORDER BY p.id DESC";

        $resultado = $conexion->query($sql);

        $prestamos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $prestamos[] = $fila;
        }

        echo json_encode($prestamos);
        exit;
    }

    if ($metodo === "POST") {

        $equipo_id = intval($datos["equipo_id"] ?? 0);
        $nombre_responsable = trim($datos["nombre_responsable"] ?? "");
        $fecha_prevista = $datos["fecha_prevista"] ?? "";
        $observaciones = trim($datos["observaciones"] ?? "");

        if ($equipo_id <= 0 || $nombre_responsable === "" || $fecha_prevista === "") {
            http_response_code(400);
            echo json_encode(["error" => "Complete los campos obligatorios."]);
            exit;
        }

        $fecha = DateTime::createFromFormat("Y-m-d", $fecha_prevista);

        if (!$fecha || $fecha->format("Y-m-d") !== $fecha_prevista) {
            http_response_code(400);
            echo json_encode(["error" => "La fecha prevista de devolución no es válida."]);
            exit;
        }

        $conexion->begin_transaction();

        $consultaEquipo = $conexion->prepare(
            "SELECT estado FROM equipos WHERE id = ? FOR UPDATE"
        );

        $consultaEquipo->bind_param("i", $equipo_id);
        $consultaEquipo->execute();

        $resultadoEquipo = $consultaEquipo->get_result();
        $equipo = $resultadoEquipo->fetch_assoc();

        if (!$equipo) {
            $conexion->rollback();
            http_response_code(404);
            echo json_encode(["error" => "El equipo no existe."]);
            exit;
        }

        if ($equipo["estado"] !== "Disponible") {
            $conexion->rollback();
            http_response_code(409);
            echo json_encode(["error" => "El equipo no está disponible para préstamo."]);
            exit;
        }

        $insertar = $conexion->prepare(
            "INSERT INTO prestamos 
            (equipo_id, nombre_responsable, fecha_prevista, estado, observaciones)
            VALUES (?, ?, ?, 'Activo', ?)"
        );

        $insertar->bind_param(
            "isss",
            $equipo_id,
            $nombre_responsable,
            $fecha_prevista,
            $observaciones
        );

        $insertar->execute();

        $actualizarEquipo = $conexion->prepare(
            "UPDATE equipos SET estado = 'Prestado' WHERE id = ?"
        );

        $actualizarEquipo->bind_param("i", $equipo_id);
        $actualizarEquipo->execute();

        $conexion->commit();

        http_response_code(201);
        echo json_encode(["mensaje" => "Préstamo registrado correctamente."]);
        exit;
    }

    if ($metodo === "PUT") {

        $id = intval($datos["id"] ?? 0);
        $accion = $datos["accion"] ?? "";

        if ($id <= 0 || $accion !== "devolver") {
            http_response_code(400);
            echo json_encode(["error" => "Solicitud de devolución no válida."]);
            exit;
        }

        $conexion->begin_transaction();

        $consultaPrestamo = $conexion->prepare(
            "SELECT equipo_id, estado FROM prestamos WHERE id = ? FOR UPDATE"
        );

        $consultaPrestamo->bind_param("i", $id);
        $consultaPrestamo->execute();

        $resultadoPrestamo = $consultaPrestamo->get_result();
        $prestamo = $resultadoPrestamo->fetch_assoc();

        if (!$prestamo) {
            $conexion->rollback();
            http_response_code(404);
            echo json_encode(["error" => "El préstamo no existe."]);
            exit;
        }

        if ($prestamo["estado"] !== "Activo") {
            $conexion->rollback();
            http_response_code(409);
            echo json_encode(["error" => "Este préstamo ya fue devuelto."]);
            exit;
        }

        $equipo_id = intval($prestamo["equipo_id"]);

        $actualizarPrestamo = $conexion->prepare(
            "UPDATE prestamos 
            SET estado = 'Devuelto', fecha_devolucion_real = CURDATE()
            WHERE id = ?"
        );

        $actualizarPrestamo->bind_param("i", $id);
        $actualizarPrestamo->execute();

        $actualizarEquipo = $conexion->prepare(
            "UPDATE equipos SET estado = 'Disponible' WHERE id = ?"
        );

        $actualizarEquipo->bind_param("i", $equipo_id);
        $actualizarEquipo->execute();

        $conexion->commit();

        echo json_encode(["mensaje" => "Equipo devuelto correctamente."]);
        exit;
    }

    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);

} catch (Throwable $error) {

    if ($conexion->errno || $conexion->error) {
        try {
            $conexion->rollback();
        } catch (Throwable $e) {
        }
    }

    http_response_code(500);
    echo json_encode(["error" => "Ocurrió un error al procesar la solicitud."]);

}

?>