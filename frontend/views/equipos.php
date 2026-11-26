<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos - Sistema de Inventario</title>
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
                <a href="equipos.php" class="activo">Equipos</a>
                <a href="prestamos.php">Préstamos</a>
                <a href="reportes.php">Reportes</a>
                	<a href="configuracion.php">Configuración</a>
            </nav>
        </aside>

        <main class="contenido">

            <header class="encabezado">
                <div>
                    <h1>Equipos</h1>
                    <p>Administración del inventario tecnológico</p>
                </div>
            </header>

            <section class="seccion">

                <div class="seccion-encabezado">
                    <h2>Listado de equipos</h2>
                    <button class="boton" id="btnAgregar">+ Agregar equipo</button>
                </div>

                <div class="tabla-contenedor">

                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Estado</th>
                                <th>Ubicación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="tablaEquipos">
                            <tr>
                                <td colspan="7">Cargando equipos...</td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </section>

        </main>

    </div>

    <div id="modalEquipo" class="modal">

        <div class="modal-contenido">

            <h2>Registrar equipo</h2>

            <form id="formEquipo">

                <label for="nombre">Nombre del equipo</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria" required>

                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca">

                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo">

                <label for="numero_serie">Número de serie</label>
                <input type="text" id="numero_serie" name="numero_serie">

                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="Disponible">Disponible</option>
                    <option value="Prestado">Prestado</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                </select>

                <label for="ubicacion">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion" required>

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"></textarea>

                <div class="modal-botones">
                    <button type="submit" class="boton">Guardar equipo</button>
                    <button type="button" class="boton cancelar" id="btnCancelar">Cancelar</button>
                </div>

            </form>

        </div>

    </div>

    <script src="../js/app.js"></script>
</body>
</html>