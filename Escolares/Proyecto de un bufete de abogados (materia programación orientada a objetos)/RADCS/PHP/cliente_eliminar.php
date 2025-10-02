<?php
/*== Almacenando datos ==*/
$user_id_del = limpiar_cadena($_GET['user_id_del']);

/*== Verificando existencia del cliente ==*/
$conexion = conexion();
$check_usuario = $conexion->query("SELECT Id_cli FROM clientes WHERE Id_cli='$user_id_del'");

if ($check_usuario->rowCount() != 1) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El cliente que intenta eliminar no existe
        </div>
    ';
    exit();
}

// Validar relaciones
$relaciones = [
    ['tabla' => 'cita', 'campo' => 'Id_cli_ct', 'mensaje' => 'citas registradas'],
    ['tabla' => 'pld', 'campo' => 'Id_cli_pld', 'mensaje' => 'pld registrados'],
    ['tabla' => 'factura', 'campo' => 'Id_cli_fc', 'mensaje' => 'facturas registradas'],
    ['tabla' => 'casos', 'campo' => 'Cli_id_cs', 'mensaje' => 'casos registradoss']

];

foreach ($relaciones as $rel) {
    $check = $conexion->query("SELECT {$rel['campo']} FROM {$rel['tabla']} WHERE {$rel['campo']}='$user_id_del' LIMIT 1");
    if ($check->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                No podemos eliminar el cliente ya que tiene ' . $rel['mensaje'] . '
            </div>
        ';
        exit();
    }
}

/*== Eliminar cliente ==*/
$eliminar_cliente = $conexion->prepare("DELETE FROM clientes WHERE Id_cli=:id");
$eliminado = $eliminar_cliente->execute([":id" => $user_id_del]);

if ($eliminado && $eliminar_cliente->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡CLIENTE ELIMINADO!</strong><br>
            Los datos del cliente se eliminaron con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo eliminar el cliente, por favor intente nuevamente
        </div>
    ';
}

$conexion = null;
?>