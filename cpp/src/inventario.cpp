
#include <iostream>
#include <string>
#include <stdexcept>
#include <fstream>
using namespace std;

// Verifica si un equipo puede prestarse
extern "C" int equipoDisponible(int cantidadDisponible) {

    if (cantidadDisponible > 0) {
        return 1;
    }

    return 0;
}

// Calcula los dias de atraso de un prestamo
extern "C" int calcularAtraso(int diasAtraso) {

    if (diasAtraso <= 0) {
        return 0;
    }

    return diasAtraso;
}

// Uso de arreglos
extern "C" int calcularTotalEquipos(int cantidades[], int tamanio) {

    int total = 0;

    for (int i = 0; i < tamanio; i++) {
        total += cantidades[i];
    }

    return total;
}

// Uso de punteros
extern "C" int duplicarCantidad(int* cantidad) {

    if (cantidad == nullptr) {
        return -1;
    }

    return (*cantidad) * 2;
}

// Manejo de cadenas
extern "C" int validarNombreEquipo(const char* nombre) {

    if (nombre == nullptr) {
        return 0;
    }

    return nombre[0] != '\0';
}

// Uso de estructuras
struct Equipo {

    int id;
    int cantidad;
    int estado;
};

extern "C" int verificarEstadoEquipo(int cantidad) {

    Equipo equipo = {1, cantidad, 0};

    if (equipo.cantidad > 0) {
        equipo.estado = 1;
    }

    return equipo.estado;
}

// Programacion orientada a objetos
class Inventario {

private:
    int total;

public:

    Inventario(int cantidad) {
        total = cantidad;
    }

    int obtenerTotal() {
        return total;
    }
};

extern "C" int obtenerInventario(int cantidad) {

    Inventario inventario(cantidad);

    return inventario.obtenerTotal();
}

// Manejo de excepciones
extern "C" int validarCantidad(int cantidad) {

    try {

        if (cantidad < 0) {
            throw invalid_argument("Cantidad invalida");
        }

        return cantidad;

    } catch (const invalid_argument&) {

        return -1;
    }
}

// Procesamiento de archivos
extern "C" int procesarArchivo() {

    // Escribir en un archivo
    ofstream archivo("registro_prueba.txt");

    if (!archivo.is_open()) {
        return 0;
    }

    archivo << "Sistema de inventario";
    archivo.close();

    // Leer el archivo
    ifstream lectura("registro_prueba.txt");

    if (!lectura.is_open()) {
        return 0;
    }

    string contenido;
    getline(lectura, contenido);
    lectura.close();

    // Verificar el contenido
    if (contenido == "Sistema de inventario") {
        return 1;
    }

    return 0;
}
