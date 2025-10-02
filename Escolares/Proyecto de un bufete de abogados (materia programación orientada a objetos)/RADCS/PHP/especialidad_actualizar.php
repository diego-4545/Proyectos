<?php
require_once "main.php";

// Verificar que se recibieron los datos necesarios
if (
    isset($_POST['id']) &&
    isset($_POST['nombre']) &&
    isset($_POST['descripcion'])
) {
    // Limpiar los datos
    $id = limpiar_cadena($_POST['id']);
    $nombre = limpiar_cadena($_POST['nombre']);
    $descripcion = limpiar_cadena($_POST['descripcion']);

    // Verificar que la especialidad existe
    $conexion = conexion();
    $check_especialidad = $conexion->query("SELECT Id_esp FROM especialidades WHERE Id_esp='$id'");
    if ($check_especialidad->rowCount() == 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                La especialidad no existe.
            </div>
        ';
        exit();
    }

    // Actualizar los datos de la especialidad
    $actualizar_especialidad = $conexion->prepare("UPDATE especialidades SET 
        No_esp=:nombre, 
        Desc_esp=:descripcion 
        WHERE Id_esp=:id");

    $actualizar_especialidad->bindParam(':nombre', $nombre);
    $actualizar_especialidad->bindParam(':descripcion', $descripcion);
    $actualizar_especialidad->bindParam(':id', $id);

    if ($actualizar_especialidad->execute()) {
        echo '
            <div class="notification is-success is-light">
                <strong>¡Especialidad actualizada!</strong><br>
                Los datos se actualizaron correctamente, actualize la pestaña para verlo reflejado.
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                No se pudo actualizar la especialidad. Intente nuevamente.
            </div>
        ';
    }

    $conexion = null;
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se recibieron todos los datos necesarios.
        </div>
    ';
}
?>