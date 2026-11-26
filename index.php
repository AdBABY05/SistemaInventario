
<?php

require_once "seguridad/auth.php";
require_once "backend/conexion.php";

// Obtener estadísticas del inventario
$totalEquipos = $conexion->query(
    "SELECT COUNT(*) AS total FROM equipos"
)->fetch_assoc()["total"];

$disponibles = $conexion->query(
    "SELECT COUNT(*) AS total FROM equipos WHERE estado = 'Disponible'"
)->fetch_assoc()["total"];

$prestados = $conexion->query(
    "SELECT COUNT(*) AS total FROM equipos WHERE estado = 'Prestado'"
)->fetch_assoc()["total"];

$mantenimiento = $conexion->query(
    "SELECT COUNT(*) AS total FROM equipos WHERE estado = 'Mantenimiento'"
)->fetch_assoc()["total"];

$prestamosActivos = $conexion->query(
    "SELECT COUNT(*) AS total FROM prestamos WHERE estado = 'Activo'"
)->fetch_assoc()["total"];

$prestamosVencidos = $conexion->query(
    "SELECT COUNT(*) AS total 
     FROM prestamos 
     WHERE estado = 'Activo' AND fecha_prevista < CURDATE()"
)->fetch_assoc()["total"];

// Obtener equipos recientes
$resultadoEquipos = $conexion->query(
    "SELECT nombre, categoria, estado, ubicacion
     FROM equipos
     ORDER BY id DESC
     LIMIT 5"
);

// Obtener préstamos recientes
$resultadoPrestamos = $conexion->query(
    "SELECT 
        p.nombre_responsable,
        p.fecha_prestamo,
        p.fecha_prevista,
        p.estado,
        e.nombre AS equipo
     FROM prestamos p
     INNER JOIN equipos e ON p.equipo_id = e.id
     ORDER BY p.id DESC
     LIMIT 5"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Inventario</title>
    <link rel="stylesheet" href="frontend/css/estilos.css">
</head>
<body>

    <div class="contenedor">

        <aside class="sidebar">

            <div class="logo">
                <h2>INVENTARIO</h2>
                <p>Gestión de equipos</p>
            </div>

            <nav class="menu">
                <a href="index.php" class="activo">Dashboard</a>
                <a href="frontend/views/equipos.php">Equipos</a>
                <a href="frontend/views/prestamos.php">Préstamos</a>
                <a href="frontend/views/reportes.php">Reportes</a>
                <a href="frontend/views/configuracion.php">Configuración</a>
            </nav>

        </aside>

        <main class="contenido">

            <header class="encabezado">
                <div>
                    <h1>Dashboard</h1>
                    <p>Resumen general del inventario tecnológico</p>
                </div>
            </header>

            <!-- ESTADISTICAS -->

            <section class="estadisticas">

                <article class="tarjeta">
                    <h3>Total de equipos</h3>
                    <p class="numero"><?php echo $totalEquipos; ?></p>
                    <span>Equipos registrados</span>
                </article>

                <article class="tarjeta">
                    <h3>Disponibles</h3>
                    <p class="numero"><?php echo $disponibles; ?></p>
                    <span>Equipos disponibles</span>
                </article>

                <article class="tarjeta">
                    <h3>Prestados</h3>
                    <p class="numero"><?php echo $prestados; ?></p>
                    <span>Equipos en préstamo</span>
                </article>

                <article class="tarjeta">
                    <h3>En mantenimiento</h3>
                    <p class="numero"><?php echo $mantenimiento; ?></p>
                    <span>Equipos en revisión</span>
                </article>

            </section>

            <!-- ESTADISTICAS DE PRESTAMOS -->

            <section class="estadisticas">

                <article class="tarjeta">
                    <h3>Préstamos activos</h3>
                    <p class="numero"><?php echo $prestamosActivos; ?></p>
                    <span>Préstamos sin devolver</span>
                </article>

                <article class="tarjeta">
                    <h3>Préstamos vencidos</h3>
                    <p class="numero"><?php echo $prestamosVencidos; ?></p>
                    <span>Devoluciones pendientes fuera de fecha</span>
                </article>

            </section>

            <!-- EQUIPOS RECIENTES -->

            <section class="seccion">

                <div class="seccion-encabezado">
                    <h2>Inventario reciente</h2>
                    <a href="frontend/views/equipos.php" class="boton">
                        Ver equipos
                    </a>
                </div>

                <div class="tabla-contenedor">

                    <table>
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Ubicación</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($resultadoEquipos && $resultadoEquipos->num_rows > 0): ?>

                                <?php while ($equipo = $resultadoEquipos->fetch_assoc()): ?>

                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($equipo["nombre"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($equipo["categoria"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($equipo["estado"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($equipo["ubicacion"]); ?>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="4">No hay equipos registrados.</td>
                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>

                </div>

            </section>

            <!-- PRESTAMOS RECIENTES -->

            <section class="seccion">

                <div class="seccion-encabezado">
                    <h2>Préstamos recientes</h2>
                    <a href="frontend/views/prestamos.php" class="boton">
                        Ver préstamos
                    </a>
                </div>

                <div class="tabla-contenedor">

                    <table>
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Responsable</th>
                                <th>Fecha de préstamo</th>
                                <th>Devolución prevista</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($resultadoPrestamos && $resultadoPrestamos->num_rows > 0): ?>

                                <?php while ($prestamo = $resultadoPrestamos->fetch_assoc()): ?>

                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($prestamo["equipo"]); ?>
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
                                            <?php echo htmlspecialchars($prestamo["estado"]); ?>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="5">No hay préstamos registrados.</td>
                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>

                </div>

            </section>

        </main>

    </div>

</body>
</html>
