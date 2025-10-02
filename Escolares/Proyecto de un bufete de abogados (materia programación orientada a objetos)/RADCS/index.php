<?php include 'INC/sesion_start.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
<?php include 'INC/Head.php'; ?>
</head>
<body>

<?php 

if(!isset($_GET['vista']) || $_GET['vista'] == '') {
    $_GET['vista'] = 'login';
}

if(is_file('VISTAS/'.$_GET['vista'].'.php') && $_GET['vista'] != 'login' && $_GET['vista'] != '404') {

    /*== Cerrar sesión si no hay datos de sesión ==*/
    if((!isset($_SESSION['id']) || $_SESSION['id']=="") || (!isset($_SESSION['correo']) || $_SESSION['correo']=="")){
        include "./vistas/logout.php";
        exit();
    }

    /*== Verificar el tipo de usuario y restringir acceso a vistas ==*/
    if (isset($_SESSION['apellidom'])) {
        // Si el usuario es abogado
        $vistas_cliente = ['home_cliente', 'citas_cliebtes', 'facturas_clientes', 'casos_clientes'];
        if (in_array($_GET['vista'], $vistas_cliente)) {
            include 'VISTAS/404.php';
            exit();
        }
        include "INC/Navbar.php"; // Cargar barra de navegación después de la verificación
    } else {
        // Si el usuario es cliente
        $vistas_abogado = ['home', 'cita', 'clientes', 'casos', 'pld','facturar','pld','especialidades','detalleae','clientes','cita','casos','abogados_list','abogados'];
        if (in_array($_GET['vista'], $vistas_abogado)) {
            include 'VISTAS/404.php';
            exit();
        }
        include "INC/Navbar_cliente.php"; // Cargar barra de navegación después de la verificación
    }

    include 'VISTAS/'.$_GET['vista'].'.php';

    include "INC/script.php";

} else {
    if($_GET['vista'] == 'login') {
        include 'VISTAS/login.php';
    } else {
        include 'VISTAS/404.php';
    }
}

?>

</body>
</html>