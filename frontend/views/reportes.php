
<?php

require_once "../../backend/conexion.php";

// Filtros
$tipo = $_GET["tipo"] ?? "equipos";
$estado = $_GET["estado"] ?? "";
$categoria = $_GET["categoria"] ?? "";
$fechaInicio = $_GET["fecha_inicio"] ?? "";
$fechaFin = $_GET["fecha_fin"] ?? "";

// Categorías disponibles
$resultadoCategorias = $conexion->query(
    "SELECT DISTINCT categoria FROM equipos ORDER BY categoria ASC"
);

$categorias = [];

while ($filaCategoria = $resultadoCategorias->fetch_assoc()) {
    $categorias[] = $filaCategoria["categoria"];
}

// Reporte de equipos
$equipos = [];

if ($tipo === "equipos") {

    $sql = "SELECT id, nombre, categoria, marca, modelo, numero_serie,
                   estado, ubicacion, fecha_registro
            FROM equipos
            WHERE 1=1";

    $parametros = [];
    $tiposParametros = "";

    if ($estado !== "") {
        $sql .= " AND estado = ?";
        $parametros[] = $estado;
        $tiposParametros .= "s";
    }

    if ($categoria !== "") {
        $sql .= " AND categoria = ?";
        $parametros[] = $categoria;
        $tiposParametros .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $consultaEquipos = $conexion->prepare($sql);

    if (!empty($parametros)) {
        $consultaEquipos->bind_param($tiposParametros, ...$parametros);
    }

    $consultaEquipos->execute();

    $resultadoEquipos = $consultaEquipos->get_result();

    while ($fila = $resultadoEquipos->fetch_assoc()) {
        $equipos[] = $fila;
    }
}

// Reporte de préstamos
$prestamos = [];

if ($tipo === "prestamos") {

    $sql = "SELECT 
                p.id,
                e.nombre AS equipo,
                e.categoria,
                p.nombre_responsable,
                p.fecha_prestamo,
                p.fecha_prevista,
                p.fecha_devolucion_real,
                p.estado,
                p.observaciones,
                CASE
                    WHEN p.estado = 'Activo' AND p.fecha_prevista < CURDATE()
                    THEN 'Vencido'
                    ELSE p.estado
                END AS estado_reporte
            FROM prestamos p
            INNER JOIN equipos e ON p.equipo_id = e.id
            WHERE 1=1";

    $parametros = [];
    $tiposParametros = "";

    if ($estado === "Vencido") {
        $sql .= " AND p.estado = 'Activo' AND p.fecha_prevista < CURDATE()";
    } elseif ($estado === "Activo") {
        $sql .= " AND p.estado = 'Activo' AND p.fecha_prevista >= CURDATE()";
    } elseif ($estado === "Devuelto") {
        $sql .= " AND p.estado = 'Devuelto'";
    }

    if ($categoria !== "") {
        $sql .= " AND e.categoria = ?";
        $parametros[] = $categoria;
        $tiposParametros .= "s";
    }

    if ($fechaInicio !== "") {
        $sql .= " AND DATE(p.fecha_prestamo) >= ?";
        $parametros[] = $fechaInicio;
        $tiposParametros .= "s";
    }

    if ($fechaFin !== "") {
        $sql .= " AND DATE(p.fecha_prestamo) <= ?";
        $parametros[] = $fechaFin;
        $tiposParametros .= "s";
    }

    $sql .= " ORDER BY p.id DESC";

    $consultaPrestamos = $conexion->prepare($sql);

    if (!empty($parametros)) {
        $consultaPrestamos->bind_param($tiposParametros, ...$parametros);
    }

    $consultaPrestamos->execute();

    $resultadoPrestamos = $consultaPrestamos->get_result();

    while ($fila = $resultadoPrestamos->fetch_assoc()) {
        $prestamos[] = $fila;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reportes - Sistema de Inventario</title>

    <link rel="stylesheet" href="../css/estilos.css">

    <style>

        .filtros-reporte {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: end;
            margin-bottom: 25px;
        }

        .campo-filtro {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 180px;
        }

        .campo-filtro label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .campo-filtro select,
        .campo-filtro input {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
        }

        .botones-reporte {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .boton-secundario {
            background-color: #64748b;
        }

        .boton-secundario:hover {
            background-color: #475569;
        }

        .estado-reporte {
            font-weight: 600;
        }

        .titulo-impresion {
            display: none;
        }

        @media print {

            body {
                background: white !important;
            }

            .sidebar,
            .filtros-reporte,
            .seccion-encabezado .boton,
            .botones-reporte {
                display: none !important;
            }

            .contenedor {
                display: block !important;
            }

            .contenido {
                width: 100% !important;
                padding: 0 !important;
            }

            .seccion {
                box-shadow: none !important;
                border: none !important;
            }

            .tabla-contenedor {
                overflow: visible !important;
            }

            table {
                width: 100% !important;
            }

            th, td {
                font-size: 10px !important;
                padding: 6px !important;
            }

            .titulo-impresion {
                display: block;
                margin-bottom: 20px;
            }

        }

    </style>

</head>

<body>

    <div class="contenedor">

        <aside class="sidebar">

            <div class="logo">
                <h2>INVENTARIO</h2>
                <p>Gestión de equipos</p>
            </div>

            <nav class="menu">
                <a href="../../index.php">Dashboard</a>
                <a href="equipos.php">Equipos</a>
                <a href="prestamos.php">Préstamos</a>
                <a href="reportes.php" class="activo">Reportes</a>
                <a href="configuracion.php">Configuración</a>
            </nav>

        </aside>

        <main class="contenido">

            <header class="encabezado">
                <div>
                    <h1>Reportes</h1>
                    <p>Consulta, filtra y genera reportes del inventario</p>
                </div>
            </header>

            <!-- FILTROS -->

            <section class="seccion">

                <div class="seccion-encabezado">
                    <h2>Filtros de consulta</h2>
                </div>

                <form method="GET" class="filtros-reporte">

                    <div class="campo-filtro">

                        <label for="tipo">Tipo de reporte</label>

                        <select name="tipo" id="tipo" onchange="this.form.submit()">

                            <option value="equipos"
                                <?php echo $tipo === "equipos" ? "selected" : ""; ?>>
                                Inventario
                            </option>

                            <option value="prestamos"
                                <?php echo $tipo === "prestamos" ? "selected" : ""; ?>>
                                Préstamos
                            </option>

                        </select>

                    </div>

                    <div class="campo-filtro">

                        <label for="estado">Estado</label>

                        <select name="estado" id="estado">

                            <option value="">Todos los estados</option>

                            <?php if ($tipo === "equipos"): ?>

                                <option value="Disponible"
                                    <?php echo $estado === "Disponible" ? "selected" : ""; ?>>
                                    Disponible
                                </option>

                                <option value="Prestado"
                                    <?php echo $estado === "Prestado" ? "selected" : ""; ?>>
                                    Prestado
                                </option>

                                <option value="Mantenimiento"
                                    <?php echo $estado === "Mantenimiento" ? "selected" : ""; ?>>
                                    Mantenimiento
                                </option>

                            <?php else: ?>

                                <option value="Activo"
                                    <?php echo $estado === "Activo" ? "selected" : ""; ?>>
                                    Activo
                                </option>

                                <option value="Devuelto"
                                    <?php echo $estado === "Devuelto" ? "selected" : ""; ?>>
                                    Devuelto
                                </option>

                                <option value="Vencido"
                                    <?php echo $estado === "Vencido" ? "selected" : ""; ?>>
                                    Vencido
                                </option>

                            <?php endif; ?>

                        </select>

                    </div>

                    <div class="campo-filtro">

                        <label for="categoria">Categoría</label>

                        <select name="categoria" id="categoria">

                            <option value="">Todas las categorías</option>

                            <?php foreach ($categorias as $cat): ?>

                                <option value="<?php echo htmlspecialchars($cat); ?>"
                                    <?php echo $categoria === $cat ? "selected" : ""; ?>>

                                    <?php echo htmlspecialchars($cat); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <?php if ($tipo === "prestamos"): ?>

                        <div class="campo-filtro">

                            <label for="fecha_inicio">Desde</label>

                            <input
                                type="date"
                                name="fecha_inicio"
                                id="fecha_inicio"
                                value="<?php echo htmlspecialchars($fechaInicio); ?>"
                            >

                        </div>

                        <div class="campo-filtro">

                            <label for="fecha_fin">Hasta</label>

                            <input
                                type="date"
                                name="fecha_fin"
                                id="fecha_fin"
                                value="<?php echo htmlspecialchars($fechaFin); ?>"
                            >

                        </div>

                    <?php endif; ?>

                    <div class="botones-reporte">

                        <button type="submit" class="boton">
                            Aplicar filtros
                        </button>

                        <a href="reportes.php" class="boton boton-secundario">
                            Limpiar
                        </a>

                        <button
                            type="button"
                            class="boton"
                            onclick="window.print()"
                        >
                            Imprimir / PDF
                        </button>

                    </div>

                </form>

            </section>

            <!-- REPORTE DE INVENTARIO -->

            <?php if ($tipo === "equipos"): ?>

                <section class="seccion">

                    <div class="titulo-impresion">
                        <h1>Reporte de inventario tecnológico</h1>
                        <p>Generado desde el Sistema de Inventario</p>
                    </div>

                    <div class="seccion-encabezado">
                        <h2>Resultados del inventario</h2>
                        <span><?php echo count($equipos); ?> registros</span>
                    </div>

                    <div class="tabla-contenedor">

                        <table>

                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N.º de serie</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (count($equipos) > 0): ?>

                                    <?php foreach ($equipos as $equipo): ?>

                                        <tr>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["nombre"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["categoria"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["marca"] ?? ""); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["modelo"] ?? ""); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["numero_serie"] ?? ""); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["estado"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($equipo["ubicacion"]); ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="7">No se encontraron equipos.</td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </section>

            <?php endif; ?>

            <!-- REPORTE DE PRESTAMOS -->

            <?php if ($tipo === "prestamos"): ?>

                <section class="seccion">

                    <div class="titulo-impresion">
                        <h1>Reporte de préstamos</h1>
                        <p>Generado desde el Sistema de Inventario</p>
                    </div>

                    <div class="seccion-encabezado">
                        <h2>Resultados de préstamos</h2>
                        <span><?php echo count($prestamos); ?> registros</span>
                    </div>

                    <div class="tabla-contenedor">

                        <table>

                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <th>Categoría</th>
                                    <th>Responsable</th>
                                    <th>Fecha préstamo</th>
                                    <th>Devolución prevista</th>
                                    <th>Devolución real</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (count($prestamos) > 0): ?>

                                    <?php foreach ($prestamos as $prestamo): ?>

                                        <tr>

                                            <td>
                                                <?php echo htmlspecialchars($prestamo["equipo"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($prestamo["categoria"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($prestamo["nombre_responsable"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($prestamo["fecha_prestamo"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($prestamo["fecha_prevista"]); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($prestamo["fecha_devolucion_real"] ?? "-"); ?>
                                            </td>

                                            <td class="estado-reporte">
                                                <?php echo htmlspecialchars($prestamo["estado_reporte"]); ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="7">No se encontraron préstamos.</td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </section>

            <?php endif; ?>

        </main>

    </div>

</body>
</html>
