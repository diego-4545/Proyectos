<?php
require_once "main.php";

// Verificar que se recibieron los datos necesarios
if (
    isset($_POST['id']) &&
    isset($_POST['cedula']) &&
    isset($_POST['nombre']) &&
    isset($_POST['apellido_paterno']) &&
    isset($_POST['apellido_materno']) &&
    isset($_POST['direccion']) &&
    isset($_POST['celular']) &&
    isset($_POST['telefono']) &&
    isset($_POST['correo'])
) {
    // Limpiar los datos
    $id = limpiar_cadena($_POST['id']);
    $cedula = limpiar_cadena($_POST['cedula']);
    $nombre = limpiar_cadena($_POST['nombre']);
    $apellido_paterno = limpiar_cadena($_POST['apellido_paterno']);
    $apellido_materno = limpiar_cadena($_POST['apellido_materno']);
    $direccion = limpiar_cadena($_POST['direccion']);
    $celular = limpiar_cadena($_POST['celular']);
    $telefono = limpiar_cadena($_POST['telefono']);
    $correo = limpiar_cadena($_POST['correo']);

    // Verificar que el abogado existe
    $conexion = conexion();
    $check_abogado = $conexion->query("SELECT Id_abgd FROM abogados WHERE Id_abgd='$id'");
    if ($check_abogado->rowCount() == 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                El abogado no existe.
            </div>
        ';
        exit();
    }

    // Actualizar los datos del abogado
    $actualizar_abogado = $conexion->prepare("UPDATE abogados SET 
        Ced_abgd=:cedula, 
        Nom_abgd=:nombre, 
        App_abgd=:apellido_paterno, 
        Apm_abgd=:apellido_materno, 
        Dir_abgd=:direccion, 
        Cel_abgd=:celular, 
        Tel_abgd=:telefono, 
        Cor_abgd=:correo 
        WHERE Id_abgd=:id");

    $actualizar_abogado->bindParam(':cedula', $cedula);
    $actualizar_abogado->bindParam(':nombre', $nombre);
    $actualizar_abogado->bindParam(':apellido_paterno', $apellido_paterno);
    $actualizar_abogado->bindParam(':apellido_materno', $apellido_materno);
    $actualizar_abogado->bindParam(':direccion', $direccion);
    $actualizar_abogado->bindParam(':celular', $celular);
    $actualizar_abogado->bindParam(':telefono', $telefono);
    $actualizar_abogado->bindParam(':correo', $correo);
    $actualizar_abogado->bindParam(':id', $id);

    if ($actualizar_abogado->execute()) {
        echo '
            <div class="notification is-success is-light">
                <strong>¡Abogado actualizado!</strong><br>
                Los datos se actualizaron correctamente, actualize la pestaña para verlo reflejado.
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                No se pudo actualizar el abogado. Intente nuevamente.
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