<?php
    
    require_once "main.php";

    /*== Almacenando datos ==*/

    $cedula=limpiar_cadena($_POST['cedula']);
    $nombre=limpiar_cadena($_POST['nombre']);
    $apellidop=limpiar_cadena($_POST['apellido_paterno']);
    $apellidom=limpiar_cadena($_POST['apellido_materno']);
    $direccion=limpiar_cadena($_POST['direccion']);
    $celular=limpiar_cadena($_POST['celular']);
    $telefono=limpiar_cadena($_POST['telefono']);
    $correo=limpiar_cadena($_POST['correo']);
    $contrasena=limpiar_cadena($_POST['contrasena']);

    /*== Verificando campos obligatorios ==*/
    if($cedula=="" || $nombre=="" || $apellidop=="" || $apellidom=="" || $direccion=="" || $celular=="" || $telefono=="" || $correo=="" || $contrasena==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ 0-9]{9}",$cedula)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}",$apellidop)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El APELLIDO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}",$apellidom)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El APELLIDO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ 0-9]{3,80}",$direccion)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[0-9]{10}",$celular)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[0-9]{10}",$telefono)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9$@.-]{7,30}",$contrasena)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La contraseña no coincide con el formato solicitado
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


    /*== Verificando usuario ==*/
    $check_usuario=conexion();
    $check_usuario=$check_usuario->query("SELECT Ced_abgd FROM abogados WHERE Ced_abgd='$cedula'");
    if($check_usuario->rowCount()>0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La cedula ingresada ya se encuentra registrada, por favor elija otra
            </div>
        ';
        exit();
    }
    $check_usuario=null;

    /*== Guardando datos ==*/
    $guardar_usuario=conexion();
    $guardar_usuario=$guardar_usuario->prepare("INSERT INTO abogados(Ced_abgd,Nom_abgd,App_abgd,Apm_abgd,Dir_abgd,Cel_abgd,Tel_abgd,Cor_abgd,Con_abgd) VALUES(:cedula,:nombre,:apellidop,:apellidom,:direccion,:celular,:telefono,:correo,:contrasena)");

    $marcadores=[
        ":cedula"=>$cedula,
        ":nombre"=>$nombre,
        ":apellidop"=>$apellidop,
        ":apellidom"=>$apellidom,
        ":direccion"=>$direccion,
        ":celular"=>$celular,
        ":telefono"=>$telefono,
        ":correo"=>$correo,
        ":contrasena"=>$contrasena
    ];

    $guardar_usuario->execute($marcadores);

    if($guardar_usuario->rowCount()==1){
        echo '
            <div class="notification is-info is-light">
                <strong>¡USUARIO REGISTRADO!</strong><br>
                El usuario se registro con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar el usuario, por favor intente nuevamente
            </div>
        ';
    }
    $guardar_usuario=null;