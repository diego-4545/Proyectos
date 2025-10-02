<?php
/*== Almacenando datos ==*/
$user_id_del = limpiar_cadena($_GET['user_id_del']);

/*== Verificando existencia de la especialidad ==*/
$conexion = conexion();
$check_especialidad = $conexion->query("SELECT Id_esp FROM especialidades WHERE Id_esp='$user_id_del'");

if ($check_especialidad->rowCount() != 1) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            La especialidad que intenta eliminar no existe
        </div>
    ';
    exit();
}

// Validar relaciones
$relaciones = [
    ['tabla' => 'detalle_ae', 'campo' => 'id_esp_dae', 'mensaje' => 'detalle ae'],
];

foreach ($relaciones as $rel) {
    $check = $conexion->query("SELECT {$rel['campo']} FROM {$rel['tabla']} WHERE {$rel['campo']}='$user_id_del' LIMIT 1");
    if ($check->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                No podemos eliminar la especialidad ya que tiene ' . $rel['mensaje'] . '
            </div>
        ';
        exit();
    }
}

/*== Eliminar especialidad ==*/
$eliminar_especialidad = $conexion->prepare("DELETE FROM especialidades WHERE Id_esp=:id");
$eliminado = $eliminar_especialidad->execute([":id" => $user_id_del]);

if ($eliminado && $eliminar_especialidad->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡ESPECIALIDAD ELIMINADA!</strong><br>
            Los datos de la especialidad se eliminaron con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo eliminar la especialidad, por favor intente nuevamente
        </div>
    ';
}

$conexion = null;
?>