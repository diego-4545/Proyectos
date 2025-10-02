<?php
require_once "main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

/*== Almacenando datos del formulario ==*/
$id_abogado = limpiar_cadena($_POST['Id_abgd']);
$id_cliente = limpiar_cadena($_POST['Id_cli']);
$fecha_cita = limpiar_cadena($_POST['fecha_cita']);
$hora_cita = limpiar_cadena($_POST['hora_cita']);

/*== Verificar conexión a la base de datos ==*/
$conexion = conexion();
if (!$conexion) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo conectar a la base de datos.
         </div>');
}

/*== Validar que la fecha no sea anterior a hoy ==*/
$fecha_actual = date("Y-m-d");
if ($fecha_cita < $fecha_actual) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pueden registrar citas con fechas anteriores a la de hoy.
         </div>');
}

/*== Validar que el abogado existe ==*/
$check_abogado = $conexion->query("SELECT Id_abgd FROM abogados WHERE Id_abgd='$id_abogado'");
if ($check_abogado->rowCount() == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            El abogado seleccionado no existe.
         </div>');
}

/*== Validar que el cliente existe ==*/
$check_cliente = $conexion->query("SELECT Id_cli FROM clientes WHERE Id_cli='$id_cliente'");
if ($check_cliente->rowCount() == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            El cliente seleccionado no existe.
         </div>');
}

/*== Validar que no exista una cita con el mismo abogado, cliente, fecha y hora ==*/
$check_cita = $conexion->prepare("
    SELECT Id_ct 
    FROM cita
    WHERE (Id_abgd_ct = :id_abogado OR Id_cli_ct = :id_cliente) 
    AND Dia_ct = :fecha_cita 
    AND Hora_ct = :hora_cita
");
$check_cita->execute([
    ":id_abogado" => $id_abogado,
    ":id_cliente" => $id_cliente,
    ":fecha_cita" => $fecha_cita,
    ":hora_cita" => $hora_cita
]);

if ($check_cita->rowCount() > 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            Ya existe una cita registrada con este abogado o cliente en la misma fecha y hora.
         </div>');
}

/*== Registrar la cita en la base de datos ==*/
$guardar_cita = $conexion->prepare("
    INSERT INTO cita (Id_abgd_ct, Id_cli_ct, Dia_ct, Hora_ct) 
    VALUES (:id_abogado, :id_cliente, :fecha_cita, :hora_cita)
");
$guardar_cita->execute([
    ":id_abogado" => $id_abogado,
    ":id_cliente" => $id_cliente,
    ":fecha_cita" => $fecha_cita,
    ":hora_cita" => $hora_cita
]);

if ($guardar_cita->rowCount() > 0) {
    echo '<div class="notification is-info is-light">
            <strong>¡Cita exitosa!</strong><br>
            La cita se registró correctamente, actualize la pestaña para verlo reflejado.
          </div>';
} else {
    echo '<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo registrar la cita. Intente nuevamente.
          </div>';
}
?>