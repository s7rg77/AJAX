<?php

require_once 'Libros.php';

/**
 * Obtiene un listado de autores.
 *
 * @return array|null Retorna un array con la lista de autores 
 * o null en caso de error.
 */
function get_listado_autores()
{
    $libros = new Libros();
    $mysqli = $libros->conexion("...", "...", "...", "...");
    if ($mysqli) {
        $lista_autores = $libros->consultarAutores($mysqli, null);
        $libros->closeConnection();
        return $lista_autores;
    }
}

/**
 * Obtiene los datos de un autor y sus libros asociados.
 *
 * @param int $id Identificador del autor.
 *
 * @return object|null Retorna un objeto con la información del autor y sus libros 
 * o null en caso de error.
 */
function get_datos_autor($id)
{
    $libros = new Libros();
    $mysqli = $libros->conexion("...", "...", "...", "...");
    if ($mysqli) {
        $info_autor = $libros->consultarDatosLibro($mysqli, $id);
        $info_autor->datos = $libros->consultarAutores($mysqli, $id);
        $info_autor->libros = $libros->consultarLibros($mysqli, $id);
        $libros->closeConnection();
        return $info_autor;
    }
}

/**
 * Obtiene un listado de libros.
 *
 * @return array|null Retorna un array con la lista de libros 
 * o null en caso de error.
 */
function get_listado_libros()
{
    $libros = new Libros();
    $mysqli = $libros->conexion("...", "...", "...", "...");
    if ($mysqli) {
        $lista_libros = $libros->consultarLibros($mysqli, "");
        $libros->closeConnection();
        return $lista_libros;
    }
}

/**
 * Obtiene los datos de un libro y su autor asociado.
 *
 * @param int $id Identificador del libro.
 *
 * @return object|null Retorna un objeto con la información del libro y su autor 
 * o null en caso de error.
 */
function get_datos_libro($id)
{
    $libros = new Libros();
    $mysqli = $libros->conexion("...", "...", "...", "...");
    if ($mysqli) {
        $info_libro = $libros->consultarDatosLibro($mysqli, $id);
        $info_libro->datos = $libros->consultarAutores($mysqli, $info_libro->id_autor);
        $libros->closeConnection();
        return $info_libro;
    }
}

/**
 * Busca libros en la base de datos que coincidan con el texto proporcionado.
 *
 * @param string $texto El texto a buscar en el título de los libros.
 *
 * @return array|null Retorna un array con los datos de los libros que coinciden con el texto 
 * proporcionado o null en caso de error.
 */
function buscarLibros($texto)
{
    $libros = new Libros();
    $mysqli = $libros->conexion("...", "...", "...", "...");
    if ($mysqli) {
        $query = "SELECT * FROM libro WHERE titulo LIKE '%$texto%'";
        $result = $mysqli->query($query);
        if ($result) {
            $datos = [];
            while ($fila = $result->fetch_object()) {
                $fila->f_publicacion = date('d/m/Y', strtotime($fila->f_publicacion));
                $datos[] = $fila;
            }
            return $datos;
        }
    }
}

$posibles_URL = array("get_listado_autores", "get_datos_autor", 
"get_listado_libros", "get_datos_libro", "buscarLibros");
$valor = "Ha ocurrido un error";
if (isset($_GET["action"]) && in_array($_GET["action"], $posibles_URL)) {
    switch ($_GET["action"]) {
        case "get_listado_autores":
            $valor = get_listado_autores();
            break;
        case "get_datos_autor":
            if (isset($_GET["id"])) {
                $valor = get_datos_autor($_GET["id"]);
            }
            break;
        case "get_listado_libros":
            $valor = get_listado_libros();
            break;
        case "get_datos_libro":
            if (isset($_GET["id"])) {
                $valor = get_datos_libro($_GET["id"]);
            }
            break;
            case "buscarLibros":
                if (isset($_GET["texto"])) {
                    $valor = buscarLibros($_GET["texto"]);
                }
                break;
    }
}

exit(json_encode($valor));

/*
function probar_get_listado_autores() {
    $result = get_listado_autores();
    var_dump($result);
}
function probar_get_datos_autor($id) {
    echo "--------------------------------------------------";
    $result = get_datos_autor($id);
    var_dump($result);
}
function probar_get_listado_libros() {
    echo "--------------------------------------------------";
    $result = get_listado_libros();
    var_dump($result);
}
function probar_get_datos_libro($id) {
    echo "--------------------------------------------------";
    $result = get_datos_libro($id);
    var_dump($result);
}
probar_get_listado_autores();
probar_get_datos_autor(0);
probar_get_listado_libros();
probar_get_datos_libro(0);
*/
?>