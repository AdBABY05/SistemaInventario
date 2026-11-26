
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos - Sistema de Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
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
                <a href="prestamos.php" class="activo">Préstamos</a>
                <a href="reportes.php">Reportes</a>
                <a href="configuracion.php">Configuración</a>
            </nav>
        </aside>

        <main class="contenido">

            <header class="encabezado">
                <div>
                    <h1>Préstamos</h1>
                    <p>Administración de préstamos y devoluciones de equipos</p>
                </div>
            </header>

            <section class="seccion">

                <div class="seccion-encabezado">
                    <h2>Listado de préstamos</h2>
                    <button class="boton" id="btnAgregarPrestamo">
                        + Registrar préstamo
                    </button>
                </div>

                <div class="tabla-contenedor">

                    <table>
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Categoría</th>
                                <th>Responsable</th>
                                <th>Fecha de préstamo</th>
                                <th>Devolución prevista</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="tablaPrestamos">
                            <tr>
                                <td colspan="7">Cargando préstamos...</td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </section>

        </main>

    </div>

    <div id="modalPrestamo" class="modal">

        <div class="modal-contenido">

            <h2>Registrar préstamo</h2>

            <form id="formPrestamo">

                <label for="equipo_id">Equipo</label>
                <select id="equipo_id" name="equipo_id" required>
                    <option value="">Cargando equipos...</option>
                </select>

                <label for="nombre_responsable">Nombre del responsable</label>
                <input
                    type="text"
                    id="nombre_responsable"
                    name="nombre_responsable"
                    maxlength="150"
                    required
                >

                <label for="fecha_prevista">Fecha prevista de devolución</label>
                <input
                    type="date"
                    id="fecha_prevista"
                    name="fecha_prevista"
                    required
                >

                <label for="observaciones">Observaciones</label>
                <textarea
                    id="observaciones"
                    name="observaciones"
                ></textarea>

                <div class="modal-botones">

                    <button type="submit" class="boton">
                        Registrar préstamo
                    </button>

                    <button
                        type="button"
                        class="boton cancelar"
                        id="btnCancelarPrestamo"
                    >
                        Cancelar
                    </button>

                </div>

            </form>

        </div>

    </div>

    <script>
    window.inventarioWasmReady = new Promise((resolve) => {

        window.Module = {
            onRuntimeInitialized: function () {
                resolve(window.Module);
            }
        };

    });
</script>

<script src="../../cpp/build/inventario.js"></script>
<script src="../js/prestamos.js"></script>

</body>
</html>
