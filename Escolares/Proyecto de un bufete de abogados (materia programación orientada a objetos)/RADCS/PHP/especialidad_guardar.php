<?php
    
require_once "main.php";

/*== Almacenando datos ==*/
$nombre = limpiar_cadena($_POST['nombre']);
$descripcion = limpiar_cadena($_POST['descripcion']);

/*== Verificando campos obligatorios ==*/
if ($nombre == "" || $descripcion == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos(".{3,500}", $descripcion)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La DESCRIPCIÓN no coincide con el formato solicitado
        </div>
    ';
    exit();
}

/*== Verificando si la especialidad ya existe ==*/
$check_nombre = conexion();
$check_nombre = $check_nombre->query("SELECT No_esp FROM especialidades WHERE No_esp='$nombre'");
if ($check_nombre->rowCount() > 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La especialidad ingresada ya se encuentra registrada, por favor elija otra
        </div>
    ';
    exit();
}
$check_nombre = null;

/*== Guardando datos ==*/
$guardar_usuario = conexion();
$guardar_usuario = $guardar_usuario->prepare("INSERT INTO especialidades(No_esp, Desc_esp) VALUES (:nombre, :descripcion)");

$marcadores = [
    ":nombre" => $nombre,
    ":descripcion" => $descripcion
];

if ($guardar_usuario->execute($marcadores)) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡ESPECIALIDAD REGISTRADA!</strong><br>
            La especialidad se registró con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo registrar la especialidad, por favor intente nuevamente
        </div>
    ';
}
$guardar_usuario = null;
?>