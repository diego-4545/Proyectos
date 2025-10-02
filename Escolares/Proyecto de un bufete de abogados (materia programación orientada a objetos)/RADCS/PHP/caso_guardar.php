<?php
require_once "main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

/*== Almacenando datos del formulario ==*/
$id_abogado = limpiar_cadena($_POST['Id_abgd']);
$id_cliente = limpiar_cadena($_POST['Id_cli']);
$nombre_caso = limpiar_cadena($_POST['nombre_caso']);
$descripcion_caso = limpiar_cadena($_POST['descripcion_caso']);

/*== Verificar conexión a la base de datos ==*/
$conexion = conexion();
if (!$conexion) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo conectar a la base de datos.
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

/*== Validar que no exista un caso con el mismo nombre para el mismo abogado y cliente ==*/
$check_caso = $conexion->prepare("
    SELECT Id_cs 
    FROM casos
    WHERE No_cs = :nombre_caso 
    AND Cli_id_cs = :id_cliente 
    AND Abgd_id_cs = :id_abogado
");
$check_caso->execute([
    ":nombre_caso" => $nombre_caso,
    ":id_cliente" => $id_cliente,
    ":id_abogado" => $id_abogado
]);

if ($check_caso->rowCount() > 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            Ya existe un caso registrado con este nombre para el mismo abogado y cliente.
         </div>');
}

/*== Registrar el caso en la base de datos ==*/
$guardar_caso = $conexion->prepare("
    INSERT INTO casos (No_cs, Desc_cs, Cli_id_cs, Abgd_id_cs) 
    VALUES (:nombre_caso, :descripcion_caso, :id_cliente, :id_abogado)
");
$guardar_caso->execute([
    ":nombre_caso" => $nombre_caso,
    ":descripcion_caso" => $descripcion_caso,
    ":id_cliente" => $id_cliente,
    ":id_abogado" => $id_abogado
]);

if ($guardar_caso->rowCount() > 0) {
    echo '<div class="notification is-info is-light">
            <strong>¡Caso registrado!</strong><br>
            El caso se registró correctamente, actualize la pestaña para verlo reflejado..
          </div>';
} else {
    echo '<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo registrar el caso. Intente nuevamente.
          </div>';
}
?>