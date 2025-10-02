<?php
/*== Almacenando datos ==*/
$cita_id_del = limpiar_cadena($_GET['cita_id_del']);

/*== Verificando existencia de la cita ==*/
$conexion = conexion();
$check_cita = $conexion->query("SELECT Id_ct FROM cita WHERE Id_ct='$cita_id_del'");

if ($check_cita->rowCount() != 1) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            La cita que intenta eliminar no existe
        </div>
    ';
    exit();
}

/*== Eliminar cita ==*/
$eliminar_cita = $conexion->prepare("DELETE FROM cita WHERE Id_ct=:id");
$eliminado = $eliminar_cita->execute([":id" => $cita_id_del]);

if ($eliminado && $eliminar_cita->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡CITA ELIMINADA!</strong><br>
            Los datos de la cita se eliminaron con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo eliminar los datos de la cita, por favor intente nuevamente
        </div>
    ';
}

$conexion = null;
?>