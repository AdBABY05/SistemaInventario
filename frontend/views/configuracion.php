
<?php

require_once "../../backend/conexion.php";

// Información básica del sistema
$totalEquipos = 0;
$totalPrestamos = 0;

$resultadoEquipos = $conexion->query("SELECT COUNT(*) AS total FROM equipos");
if ($resultadoEquipos) {
    $totalEquipos = $resultadoEquipos->fetch_assoc()["total"];
}

$resultadoPrestamos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos");
if ($resultadoPrestamos) {
    $totalPrestamos = $resultadoPrestamos->fetch_assoc()["total"];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configuración - Sistema de Inventario</title>

    <link rel="stylesheet" href="../css/estilos.css">

    <style>

        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .config-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px;
        }

        .config-card h3 {
            margin-bottom: 12px;
            color: #1e293b;
        }

        .config-card p {
            color: #64748b;
            line-height: 1.6;
        }

        .config-dato {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 15px;
        }

        .config-seccion {
            margin-bottom: 25px;
        }

        .config-lista {
            list-style: none;
            padding: 0;
        }

        .config-lista li {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        .config-lista li:last-child {
            border-bottom: none;
        }

        .config-etiqueta {
            font-weight: 600;
            color: #1e293b;
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
                <a href="reportes.php">Reportes</a>
                <a href="configuracion.php" class="activo">Configuración</a>
            </nav>

        </aside>

        <main class="contenido">

            <header class="encabezado">
                <div>
                    <h1>Configuración</h1>
                    <p>Información general del SistemaInventario</p>
                </div>
            </header>

            <section class="seccion config-seccion">

                <div class="seccion-encabezado">
                    <h2>Información del sistema</h2>
                </div>

                <div class="config-grid">

                    <div class="config-card">
                        <h3>Nombre</h3>
                        <p>Sistema de Inventario Tecnológico</p>
                    </div>

                    <div class="config-card">
                        <h3>Versión</h3>
                        <p>1.0.0</p>
                    </div>

                    <div class="config-card">
                        <h3>Plataforma</h3>
                        <p>Aplicación web local desarrollada con PHP, MySQL, HTML, CSS y JavaScript.</p>
                    </div>

                    <div class="config-card">
                        <h3>Estado</h3>
                        <p>Operativo</p>
                    </div>

                </div>

            </section>

            <section class="seccion config-seccion">

                <div class="seccion-encabezado">
                    <h2>Resumen de la base de datos</h2>
                </div>

                <div class="config-grid">

                    <div class="config-card">
                        <h3>Equipos registrados</h3>
                        <p>Equipos almacenados en el inventario.</p>
                        <div class="config-dato">
                            <?php echo $totalEquipos; ?>
                        </div>
                    </div>

                    <div class="config-card">
                        <h3>Préstamos registrados</h3>
                        <p>Registros históricos y actuales de préstamos.</p>
                        <div class="config-dato">
                            <?php echo $totalPrestamos; ?>
                        </div>
                    </div>

                </div>

            </section>

            <section class="seccion config-seccion">

                <div class="seccion-encabezado">
                    <h2>Componentes tecnológicos</h2>
                </div>

                <ul class="config-lista">

                    <li>
                        <span class="config-etiqueta">Frontend:</span>
                        HTML5, CSS3 y JavaScript.
                    </li>

                    <li>
                        <span class="config-etiqueta">Backend:</span>
                        PHP.
                    </li>

                    <li>
                        <span class="config-etiqueta">Base de datos:</span>
                        MySQL.
                    </li>

                    <li>
                        <span class="config-etiqueta">Servidor local:</span>
                        Apache mediante XAMPP.
                    </li>

                    <li>
                        <span class="config-etiqueta">Entorno de desarrollo:</span>
                        Visual Studio Code.
                    </li>

                </ul>

            </section>

            <section class="seccion">

                <div class="seccion-encabezado">
                    <h2>Acerca del sistema</h2>
                </div>

                <p style="line-height: 1.8; color: #475569;">

                    SistemaInventario es una aplicación web destinada a la
                    administración y control de equipos tecnológicos.
                    Permite registrar, consultar, editar y eliminar equipos,
                    gestionar préstamos y devoluciones, visualizar información
                    general mediante un Dashboard y generar reportes del
                    inventario y de los préstamos registrados.

                </p>

            </section>

        </main>

    </div>

</body>
</html>
