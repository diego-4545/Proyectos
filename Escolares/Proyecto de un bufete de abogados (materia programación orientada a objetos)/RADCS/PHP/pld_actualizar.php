<?php
require_once "main.php";

// Verificar que se recibieron los datos necesarios
if (
    isset($_POST['Id_pld']) &&
    isset($_POST['Fe_pld']) &&
    isset($_POST['Desc_pld']) &&
    isset($_POST['Fepg_pld']) &&
    isset($_POST['Im_pld']) &&
    isset($_POST['Ipg_pld']) &&
    isset($_POST['Edo_pld']) &&
    isset($_POST['Tot_pld']) &&
    isset($_POST['Id_cli_pld']) &&
    isset($_POST['Id_abgd_pld'])
) {
    // Limpiar los datos
    $id_pld = limpiar_cadena($_POST['Id_pld']);
    $fe_pld = limpiar_cadena($_POST['Fe_pld']);
    $desc_pld = limpiar_cadena($_POST['Desc_pld']);
    $fepg_pld = limpiar_cadena($_POST['Fepg_pld']);
    $im_pld = limpiar_cadena($_POST['Im_pld']);
    $ipg_pld = limpiar_cadena($_POST['Ipg_pld']);
    $edo_pld = limpiar_cadena($_POST['Edo_pld']);
    $tot_pld = limpiar_cadena($_POST['Tot_pld']);
    $id_cli_pld = limpiar_cadena($_POST['Id_cli_pld']);
    $id_abgd_pld = limpiar_cadena($_POST['Id_abgd_pld']);

    // Validar que el importe pagado no sea mayor al importe total
    if ($ipg_pld > $im_pld) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                El importe pagado no puede ser mayor al importe total.
            </div>
        ';
        exit();
    }
    // Determinar el estado (Edo_pld)
    $edo_pld = ($ipg_pld == $im_pld) ? "Pagado" : "Pendiente";

    // Calcular el total (Tot_pld)
    $tot_pld = $ipg_pld;

    // Verificar que el registro existe
    $conexion = conexion();
    $check_pld = $conexion->query("SELECT Id_pld FROM pld WHERE Id_pld='$id_pld'");
    if ($check_pld->rowCount() == 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                El registro de pago no existe.
            </div>
        ';
        exit();
    }

    // Actualizar los datos del registro
    $actualizar_pld = $conexion->prepare("UPDATE pld SET 
        Fe_pld=:fe_pld,
        Desc_pld=:desc_pld, 
        Fepg_pld=:fepg_pld, 
        Im_pld=:im_pld, 
        Ipg_pld=:ipg_pld, 
        Edo_pld=:edo_pld, 
        Tot_pld=:tot_pld, 
        Id_cli_pld=:id_cli_pld, 
        Id_abgd_pld=:id_abgd_pld 
        WHERE Id_pld=:id_pld");

    $actualizar_pld->bindParam(':fe_pld', $fe_pld);
    $actualizar_pld->bindParam(':desc_pld', $desc_pld);
    $actualizar_pld->bindParam(':fepg_pld', $fepg_pld);
    $actualizar_pld->bindParam(':im_pld', $im_pld);
    $actualizar_pld->bindParam(':ipg_pld', $ipg_pld);
    $actualizar_pld->bindParam(':edo_pld', $edo_pld);
    $actualizar_pld->bindParam(':tot_pld', $tot_pld);
    $actualizar_pld->bindParam(':id_cli_pld', $id_cli_pld);
    $actualizar_pld->bindParam(':id_abgd_pld', $id_abgd_pld);
    $actualizar_pld->bindParam(':id_pld', $id_pld);

    if ($actualizar_pld->execute()) {
        echo '
            <div class="notification is-success is-light">
                <strong>¡Registro actualizado!</strong><br>
                Los datos del pago se actualizaron correctamente, actualize la pestaña para verlo reflejado.
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Error!</strong><br>
                No se pudo actualizar el registro. Intente nuevamente.
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
