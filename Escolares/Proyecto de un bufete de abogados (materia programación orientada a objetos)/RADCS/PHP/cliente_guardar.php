<?php

require_once "main.php";

/*== Almacenando datos ==*/
$nombre = limpiar_cadena($_POST['nombre']);
$apellidop = limpiar_cadena($_POST['apellido_paterno']);
$apellidom = limpiar_cadena($_POST['apellido_materno']);
$direccion = limpiar_cadena($_POST['direccion']);
$codigo_postal = limpiar_cadena($_POST['codigo_postal']);
$regimen_fiscal = limpiar_cadena($_POST['regimen_fiscal']);
$telefono = limpiar_cadena($_POST['telefono']);
$correo = limpiar_cadena($_POST['correo']);
$contrasena = limpiar_cadena($_POST['contrasena']);

/*== Verificando campos obligatorios ==*/
if ($nombre == "" || $apellidop == "" || $apellidom == "" || $direccion == "" || $codigo_postal == "" || $regimen_fiscal == "" || $telefono == "" || $correo == "" || $contrasena == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

/*== Validando datos ==*/
if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}", $apellidop)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El APELLIDO PATERNO no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}", $apellidom)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El APELLIDO MATERNO no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ 0-9]{3,80}", $direccion)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            La DIRECCIÓN no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[0-9]{5}", $codigo_postal)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El CÓDIGO POSTAL no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $regimen_fiscal)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El RÉGIMEN FISCAL no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[0-9]{10}", $telefono)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El TELÉFONO no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9$@.-]{7,30}", $contrasena)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            La CONTRASEÑA no coincide con el formato solicitado
        </div>
    ';
    exit();
}

/*== Verificando email ==*/
if ($correo != "") {
    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $conexion = conexion();

        // Verificar en la tabla de clientes
        $check_email_cliente = $conexion->query("SELECT Cor_cli FROM clientes WHERE Cor_cli='$correo'");
        if ($check_email_cliente->rowCount() > 0) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    El correo electrónico ingresado ya se encuentra registrado en la tabla de clientes, por favor elija otro
                </div>
            ';
            exit();
        }

        // Verificar en la tabla de abogados
        $check_email_abogado = $conexion->query("SELECT Cor_abgd FROM abogados WHERE Cor_abgd='$correo'");
        if ($check_email_abogado->rowCount() > 0) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    El correo electrónico ingresado ya se encuentra registrado en la tabla de abogados, por favor elija otro
                </div>
            ';
            exit();
        }

        $check_email_cliente = null;
        $check_email_abogado = null;
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                Ha ingresado un correo electrónico no válido
            </div>
        ';
        exit();
    }
}

/*== Guardando datos ==*/
$guardar_usuario = conexion();
$guardar_usuario = $guardar_usuario->prepare("INSERT INTO clientes(Nom_cli, App_cli, Apm_cli, Dir_cli, Cp_cli, Rf_cli, Tel_cli, Cor_cli, Con_cli) VALUES(:nombre, :apellidop, :apellidom, :direccion, :codigo_postal, :regimen_fiscal, :telefono, :correo, :contrasena)");

$marcadores = [
    ":nombre" => $nombre,
    ":apellidop" => $apellidop,
    ":apellidom" => $apellidom,
    ":direccion" => $direccion,
    ":codigo_postal" => $codigo_postal,
    ":regimen_fiscal" => $regimen_fiscal,
    ":telefono" => $telefono,
    ":correo" => $correo,
    ":contrasena" => $contrasena
];

$guardar_usuario->execute($marcadores);

if ($guardar_usuario->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡CLIENTE REGISTRADO!</strong><br>
            El cliente se registró con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo registrar el cliente, por favor intente nuevamente
        </div>
    ';
}
$guardar_usuario = null;
?>