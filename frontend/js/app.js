const API = "../../backend/equipos.php";

const modal = document.getElementById("modalEquipo");
const btnAgregar = document.getElementById("btnAgregar");
const tituloModal = modal.querySelector("h2");
const btnCancelar = document.getElementById("btnCancelar");
const formEquipo = document.getElementById("formEquipo");
const tablaEquipos = document.getElementById("tablaEquipos");

btnAgregar.addEventListener("click", () => {
    formEquipo.reset();
    delete formEquipo.dataset.id;
    tituloModal.textContent = "Registrar equipo";
    modal.style.display = "flex";
});

btnCancelar.addEventListener("click", () => {
    modal.style.display = "none";
    formEquipo.reset();
    delete formEquipo.dataset.id;
    tituloModal.textContent = "Registrar equipo";
});

modal.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
        formEquipo.reset();
        delete formEquipo.dataset.id;
        tituloModal.textContent = "Registrar equipo";
    }
});

async function cargarEquipos() {
    try {
        const respuesta = await fetch(API);
        const equipos = await respuesta.json();

        tablaEquipos.innerHTML = "";

        if (equipos.length === 0) {
            tablaEquipos.innerHTML = `
                <tr>
                    <td colspan="7">No hay equipos registrados.</td>
                </tr>
            `;
            return;
        }

        equipos.forEach(equipo => {
            const fila = document.createElement("tr");

            [
                equipo.nombre,
                equipo.categoria,
                equipo.marca,
                equipo.modelo,
                equipo.estado,
                equipo.ubicacion
            ].forEach(valor => {
                const celda = document.createElement("td");
                celda.textContent = valor || "-";
                fila.appendChild(celda);
            });

            const celdaAcciones = document.createElement("td");

            const btnEditar = document.createElement("button");
            btnEditar.textContent = "Editar";
            btnEditar.className = "boton";

            btnEditar.addEventListener("click", () => {
                formEquipo.dataset.id = equipo.id;

                document.getElementById("nombre").value = equipo.nombre;
                document.getElementById("categoria").value = equipo.categoria;
                document.getElementById("marca").value = equipo.marca || "";
                document.getElementById("modelo").value = equipo.modelo || "";
                document.getElementById("numero_serie").value = equipo.numero_serie || "";
                document.getElementById("estado").value = equipo.estado;
                document.getElementById("ubicacion").value = equipo.ubicacion;
                document.getElementById("descripcion").value = equipo.descripcion || "";

                tituloModal.textContent = "Editar equipo";
                modal.style.display = "flex";
            });

            const btnEliminar = document.createElement("button");
            btnEliminar.textContent = "Eliminar";
            btnEliminar.className = "boton cancelar";

            btnEliminar.addEventListener("click", async () => {
                const confirmar = confirm("¿Deseas eliminar este equipo?");

                if (!confirmar) {
                    return;
                }

                try {
                    const respuesta = await fetch(API, {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ id: equipo.id })
                    });

                    const resultado = await respuesta.json();

                    if (resultado.error) {
                        alert(resultado.error);
                        return;
                    }

                    alert("Equipo eliminado correctamente.");
                    cargarEquipos();

                } catch (error) {
                    alert("No fue posible eliminar el equipo.");
                }
            });

            celdaAcciones.appendChild(btnEditar);
            celdaAcciones.appendChild(btnEliminar);

            fila.appendChild(celdaAcciones);
            tablaEquipos.appendChild(fila);
        });

    } catch (error) {
        tablaEquipos.innerHTML = `
            <tr>
                <td colspan="7">Error al cargar los equipos.</td>
            </tr>
        `;
    }
}

formEquipo.addEventListener("submit", async (e) => {
    e.preventDefault();

    const idEdicion = formEquipo.dataset.id;
    const datos = Object.fromEntries(new FormData(formEquipo).entries());

    if (idEdicion) {
        datos.id = idEdicion;
    }

    try {
        const respuesta = await fetch(API, {
            method: idEdicion ? "PUT" : "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.error) {
            alert(resultado.error);
            return;
        }

        alert(idEdicion ? "Equipo actualizado correctamente." : "Equipo registrado correctamente.");

        formEquipo.reset();
        delete formEquipo.dataset.id;
        tituloModal.textContent = "Registrar equipo";
        modal.style.display = "none";

        cargarEquipos();

    } catch (error) {
        alert("No fue posible guardar el equipo.");
    }
});

cargarEquipos();