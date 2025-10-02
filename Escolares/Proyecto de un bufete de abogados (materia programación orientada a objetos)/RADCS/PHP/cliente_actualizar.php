<?php
require_once "main.php";

// Verificar que se recibieron los datos necesarios
if (
    isset($_POST['id']) &&
    isset($_POST['nombre']) &&
    isset($_POST['apellido_paterno']) &&
    isset($_POST['apellido_materno']) &&
    isset($_POST['direccion']) &&
    isset($_POST['codigo_postal']) &&
    isset($_POST['regimen_fiscal']) &&
    isset($_POST['telefono']) &&
    isset($_POST['correo'])
) {
    // Limpiar los datos
    $id = limpiar_cadena($_POST['id']);
    $nombre = limpiar_cadena($_POST['nombre']);
    $apellido_paterno = limpiar_cadena($_POST['apellido_paterno']);
    $apellido_materno = limpiar_cadena($_POST['apellido_materno']);
    $direccion = limpiar_cadena($_POST['direccion']);
    $codigo_postal = limpiar_cadena($_POST['codigo_postal']);
    $regimen_fiscal = limpiar_cadena($_POST['regimen_fiscal']);
    $telefono = limpiar_cadena($_POST['telefono']);
    $correo = limpiar_cadena($_POST['correo']);

    // Verificar que el cliente existe
    $conexion = conexion();
    $check_cliente = $conexion->query("SELECT Id_cli FROM clientes WHERE Id_cli='$id'");
    if ($check_cliente->rowCount() == 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                El cliente no existe.
            </div>
        ';
        exit();
    }

    // Actualizar los datos del cliente
    $actualizar_cliente = $conexion->prepare("UPDATE clientes SET 
        Nom_cli=:nombre, 
        App_cli=:apellido_paterno, 
        Apm_cli=:apellido_materno, 
        Dir_cli=:direccion, 
        Cp_cli=:codigo_postal, 
        Rf_cli=:regimen_fiscal, 
        Tel_cli=:telefono, 
        Cor_cli=:correo 
        WHERE Id_cli=:id");

    $actualizar_cliente->bindParam(':nombre', $nombre);
    $actualizar_cliente->bindParam(':apellido_paterno', $apellido_paterno);
    $actualizar_cliente->bindParam(':apellido_materno', $apellido_materno);
    $actualizar_cliente->bindParam(':direccion', $direccion);
    $actualizar_cliente->bindParam(':codigo_postal', $codigo_postal);
    $actualizar_cliente->bindParam(':regimen_fiscal', $regimen_fiscal);
    $actualizar_cliente->bindParam(':telefono', $telefono);
    $actualizar_cliente->bindParam(':correo', $correo);
    $actualizar_cliente->bindParam(':id', $id);

    if ($actualizar_cliente->execute()) {
        echo '
            <div class="notification is-success is-light">
                <strong>¡Cliente actualizado!</strong><br>
                Los datos se actualizaron correctamente, actualize la pestaña para verlo reflejado.
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                No se pudo actualizar el cliente. Intente nuevamente.
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