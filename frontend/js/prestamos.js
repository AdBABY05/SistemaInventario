
const API_PRESTAMOS = "../../backend/prestamos.php";
const API_EQUIPOS = "../../backend/equipos.php";

const modal = document.getElementById("modalPrestamo");
const btnAgregar = document.getElementById("btnAgregarPrestamo");
const btnCancelar = document.getElementById("btnCancelarPrestamo");
const formPrestamo = document.getElementById("formPrestamo");
const selectorEquipo = document.getElementById("equipo_id");
const tablaPrestamos = document.getElementById("tablaPrestamos");

// Validacion mediante C++ y WebAssembly
async function validarDisponibilidadWasm() {

    try {

        const modulo = await window.inventarioWasmReady;

        const equipoSeleccionado = selectorEquipo.value;

        const cantidadDisponible = equipoSeleccionado !== "" ? 1 : 0;

        const resultado = modulo.ccall(
            "equipoDisponible",
            "number",
            ["number"],
            [cantidadDisponible]
        );

        return resultado === 1;

    } catch (error) {

        console.error("Error en WebAssembly:", error);
        return false;

    }

}

async function cargarEquiposDisponibles() {
    try {
        const respuesta = await fetch(API_EQUIPOS);
        const equipos = await respuesta.json();

        selectorEquipo.innerHTML = "";

        const disponibles = equipos.filter(equipo => equipo.estado === "Disponible");

        if (disponibles.length === 0) {
            selectorEquipo.innerHTML = `
                <option value="">No hay equipos disponibles</option>
            `;
            return false;
        }

        selectorEquipo.innerHTML = `
            <option value="">Seleccione un equipo</option>
        `;

        disponibles.forEach(equipo => {
            const opcion = document.createElement("option");

            opcion.value = equipo.id;
            opcion.textContent = `${equipo.nombre} - ${equipo.categoria}`;

            selectorEquipo.appendChild(opcion);
        });

        return true;

    } catch (error) {
        selectorEquipo.innerHTML = `
            <option value="">Error al cargar equipos</option>
        `;
        return false;
    }
}

async function cargarPrestamos() {
    try {
        const respuesta = await fetch(API_PRESTAMOS);
        const prestamos = await respuesta.json();

        tablaPrestamos.innerHTML = "";

        if (!Array.isArray(prestamos) || prestamos.length === 0) {
            tablaPrestamos.innerHTML = `
                <tr>
                    <td colspan="7">No hay préstamos registrados.</td>
                </tr>
            `;
            return;
        }

        prestamos.forEach(prestamo => {
            const fila = document.createElement("tr");

            const datos = [
                prestamo.equipo,
                prestamo.categoria,
                prestamo.nombre_responsable,
                prestamo.fecha_prestamo,
                prestamo.fecha_prevista,
                prestamo.estado
            ];

            datos.forEach(valor => {
                const celda = document.createElement("td");
                celda.textContent = valor || "-";
                fila.appendChild(celda);
            });

            const celdaAcciones = document.createElement("td");

            if (prestamo.estado === "Activo") {
                const btnDevolver = document.createElement("button");

                btnDevolver.textContent = "Devolver";
                btnDevolver.className = "boton";

                btnDevolver.addEventListener("click", async () => {
                    const confirmar = confirm(
                        `¿Confirmas la devolución del equipo ${prestamo.equipo}?`
                    );

                    if (!confirmar) {
                        return;
                    }

                    try {
                        const respuesta = await fetch(API_PRESTAMOS, {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id: prestamo.id,
                                accion: "devolver"
                            })
                        });

                        const resultado = await respuesta.json();

                        if (!respuesta.ok || resultado.error) {
                            alert(resultado.error || "No fue posible registrar la devolución.");
                            return;
                        }

                        alert("Equipo devuelto correctamente.");

                        cargarPrestamos();

                    } catch (error) {
                        alert("Error al procesar la devolución.");
                    }
                });

                celdaAcciones.appendChild(btnDevolver);

            } else {
                celdaAcciones.textContent = "Sin acciones";
            }

            fila.appendChild(celdaAcciones);
            tablaPrestamos.appendChild(fila);
        });

    } catch (error) {
        tablaPrestamos.innerHTML = `
            <tr>
                <td colspan="7">Error al cargar los préstamos.</td>
            </tr>
        `;
    }
}

btnAgregar.addEventListener("click", async () => {
    formPrestamo.reset();

    const hayDisponibles = await cargarEquiposDisponibles();

    if (!hayDisponibles) {
        alert("No hay equipos disponibles para préstamo.");
        return;
    }

    modal.style.display = "flex";
});

btnCancelar.addEventListener("click", () => {
    modal.style.display = "none";
    formPrestamo.reset();
});

modal.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
        formPrestamo.reset();
    }
});

formPrestamo.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Validar disponibilidad mediante C++/WebAssembly
    const equipoValido = await validarDisponibilidadWasm();

    if (!equipoValido) {
        alert("Seleccione un equipo válido para el préstamo.");
        return;
    }

    const datos = Object.fromEntries(new FormData(formPrestamo).entries());

    try {
        const respuesta = await fetch(API_PRESTAMOS, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok || resultado.error) {
            alert(resultado.error || "No fue posible registrar el préstamo.");
            return;
        }

        alert("Préstamo registrado correctamente.");

        modal.style.display = "none";
        formPrestamo.reset();

        cargarPrestamos();

    } catch (error) {
        alert("Error al registrar el préstamo.");
    }
});

cargarPrestamos();
