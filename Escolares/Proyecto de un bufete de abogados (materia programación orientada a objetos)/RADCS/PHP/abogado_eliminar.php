<?php
/*== Almacenando datos ==*/
$user_id_del=limpiar_cadena($_GET['user_id_del']);

/*== Almacenando datos ==*/
$user_id_del = limpiar_cadena($_GET['user_id_del']);

/*== Verificando existencia del abogado ==*/
$conexion = conexion();
$check_usuario = $conexion->query("SELECT Id_abgd FROM abogados WHERE Id_abgd='$user_id_del'");

if ($check_usuario->rowCount() != 1) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El abogado que intenta eliminar no existe
        </div>
    ';
    exit();
}

// Validar relaciones
$relaciones = [
    ['tabla' => 'casos', 'campo' => 'Abgd_id_cs', 'mensaje' => 'casos registrados'],
    ['tabla' => 'cita', 'campo' => 'Id_abgd_ct', 'mensaje' => 'citas'],
    ['tabla' => 'detalle_ae', 'campo' => 'id_abgd_dae', 'mensaje' => 'una especialidad asignada'],
    ['tabla' => 'pld', 'campo' => 'Id_abgd_pld', 'mensaje' => 'pld']
];

foreach ($relaciones as $rel) {
    $check = $conexion->query("SELECT {$rel['campo']} FROM {$rel['tabla']} WHERE {$rel['campo']}='$user_id_del' LIMIT 1");
    if ($check->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                No podemos eliminar el abogado ya que tiene ' . $rel['mensaje'] . '
            </div>
        ';
        exit();
    }
}

/*== Eliminar abogado ==*/
$eliminar_abogado = $conexion->prepare("DELETE FROM abogados WHERE Id_abgd=:id");
$eliminado = $eliminar_abogado->execute([":id" => $user_id_del]);

if ($eliminado && $eliminar_abogado->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡ABOGADO ELIMINADO!</strong><br>
            Los datos del abogado se eliminaron con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo eliminar el abogado, por favor intente nuevamente
        </div>
    ';
}

$conexion = null;
?>