<?php

/*== Almacenando datos ==*/
$correo = limpiar_cadena($_POST['correo']);
$contrasena = limpiar_cadena($_POST['contrasena']);

/*== Verificando campos obligatorios ==*/
if ($correo == "" || $contrasena == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9$@.-]{7,30}", $contrasena)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            Las CLAVE no coinciden con el formato solicitado
        </div>
    ';
    exit();
}

/*== Verificar si es abogado ==*/
$check_user = conexion();
$check_user = $check_user->query("SELECT * FROM abogados WHERE Cor_abgd='$correo'");

if ($check_user->rowCount() == 1) {
    $check_user = $check_user->fetch();

    if ($check_user['Cor_abgd'] == $correo && $check_user['Con_abgd'] == $contrasena) {
        // Iniciar sesión como abogado
        $_SESSION['id'] = $check_user['Id_abgd'];
        $_SESSION['nombre'] = $check_user['Nom_abgd'];
        $_SESSION['apellidop'] = $check_user['App_abgd'];
        $_SESSION['apellidom'] = $check_user['Apm_abgd'];
        $_SESSION['correo'] = $check_user['Cor_abgd'];

        if (headers_sent()) {
            echo "<script> window.location.href='index.php?vista=home'; </script>";
        } else {
            header("Location: index.php?vista=home");
        }
        exit();
    }
}

/*== Verificar si es cliente ==*/
$check_user = conexion();
$check_user = $check_user->query("SELECT * FROM clientes WHERE Cor_cli='$correo'");

if ($check_user->rowCount() == 1) {
    $check_user = $check_user->fetch();

    if ($check_user['Cor_cli'] == $correo && $check_user['Con_cli'] == $contrasena) {
        // Iniciar sesión como cliente
        $_SESSION['id'] = $check_user['Id_cli'];
        $_SESSION['nombre'] = $check_user['Nom_cli'];
        $_SESSION['apellidop'] = $check_user['App_cli'];
        $_SESSION['correo'] = $check_user['Cor_cli'];

        if (headers_sent()) {
            echo "<script> window.location.href='index.php?vista=home_cliente'; </script>";
        } else {
            header("Location: index.php?vista=home_cliente");
        }
        exit();
    }
}

/*== Si no se encuentra el usuario ==*/
echo '
    <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        Correo o contraseña incorrectos
    </div>
';
$check_user = null;
?>