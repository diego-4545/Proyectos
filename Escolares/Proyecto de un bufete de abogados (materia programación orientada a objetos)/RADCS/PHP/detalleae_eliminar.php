<?php
/*== Almacenando datos ==*/
$user_id_del = limpiar_cadena($_GET['user_id_del']);

/*== Verificando existencia del detalle ==*/
$conexion = conexion();
$check_especialidad = $conexion->query("SELECT id_dae FROM detalle_ae WHERE id_dae='$user_id_del'");

if ($check_especialidad->rowCount() != 1) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El detalle ae que intenta eliminar no existe
        </div>
    ';
    exit();
}


/*== Eliminar especialidad ==*/
$eliminar_especialidad = $conexion->prepare("DELETE FROM detalle_ae WHERE id_dae=:id");
$eliminado = $eliminar_especialidad->execute([":id" => $user_id_del]);

if ($eliminado && $eliminar_especialidad->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡DETALLE AE ELIMINADO!</strong><br>
            Los datos del detalle ae se eliminaron con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo eliminar los datos del detalle, por favor intente nuevamente
        </div>
    ';
}

$conexion = null;
?>

