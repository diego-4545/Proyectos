<?php
require_once "main.php";

/*== Almacenando datos ==*/
$id_cliente = limpiar_cadena($_POST['Id_cli']);
$fecha = date("Y-m-d"); // Fecha actual
$descripcion = limpiar_cadena($_POST['Descripcion']);
$fecha_pago = limpiar_cadena($_POST['Fecha_pago']);
$importe = limpiar_cadena($_POST['Importe']);
$importe_pagado = limpiar_cadena($_POST['Importe_pagado']);
$id_abogado = limpiar_cadena($_POST['Id_abgd']);

/*== Verificando campos obligatorios ==*/
if ($id_cliente == "" || $descripcion == "" || $fecha_pago == "" || $importe == "" || $importe_pagado == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios.
        </div>
    ';
    exit();
}

/*== Validando datos ==*/
if (!is_numeric($importe) || $importe <= 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El IMPORTE debe ser un número mayor a 0.
        </div>
    ';
    exit();
}

if (!is_numeric($importe_pagado) || $importe_pagado < 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El IMPORTE PAGADO debe ser un número mayor o igual a 0.
        </div>
    ';
    exit();
}

if ($importe_pagado > $importe) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El IMPORTE PAGADO no puede ser mayor al IMPORTE total.
        </div>
    ';
    exit();
}

/*== Determinando el estado ==*/

$estado = ($importe_pagado == $importe) ? "Pagado" : "Pendiente";

/*== Guardando datos ==*/
$guardar_pld = conexion();
$guardar_pld = $guardar_pld->prepare("
    INSERT INTO pld (Fe_pld, Desc_pld, Fepg_pld, Im_pld, Ipg_pld, Edo_pld, Tot_pld, Id_cli_pld, Id_abgd_pld) 
    VALUES (:fecha, :descripcion, :fecha_pago, :importe, :importe_pagado, :estado, :total, :id_cliente, :id_abogado)
");

$marcadores = [
    ":fecha" => $fecha,
    ":descripcion" => $descripcion,
    ":fecha_pago" => $fecha_pago,
    ":importe" => $importe,
    ":importe_pagado" => $importe_pagado,
    ":estado" => $estado,
    ":total" => $importe_pagado,
    ":id_cliente" => $id_cliente,
    ":id_abogado" => $id_abogado
];

if ($guardar_pld->execute($marcadores)) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡REGISTRO GUARDADO!</strong><br>
            El registro se guardó con éxito, actualize la pestaña para verlo reflejado.
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No se pudo guardar el registro, por favor intente nuevamente.
        </div>
    ';
}
$guardar_pld = null;
?>