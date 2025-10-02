<?php
require_once "main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

/*== Almacenando datos del formulario ==*/
$id_abogado = limpiar_cadena($_POST['Id_abgd']);
$id_especialidades = $_POST['Id_esp']; // Esto es un array

/*== Verificar conexión a la base de datos ==*/
$conexion = conexion();
if (!$conexion) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo conectar a la base de datos.
         </div>');
}

/*== Verificar que el abogado existe ==*/
$check_abogado = $conexion->query("SELECT Id_abgd FROM abogados WHERE Id_abgd='$id_abogado'");
if ($check_abogado->rowCount() == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            El abogado seleccionado no existe.
         </div>');
}

/*== Guardar cada especialidad seleccionada en la tabla detalleae ==*/
foreach ($id_especialidades as $id_especialidad) {
    // Verificar si ya existe la relación entre el abogado y la especialidad
    $check_relacion = $conexion->prepare("SELECT id_abgd_dae FROM detalle_ae WHERE id_abgd_dae = :id_abogado AND id_esp_dae = :id_especialidad");
    $check_relacion->execute([
        ":id_abogado" => $id_abogado,
        ":id_especialidad" => $id_especialidad
    ]);

    if ($check_relacion->rowCount() == 0) {
        // Si no existe la relación, insertar el registro
        $guardar_detalle = $conexion->prepare("INSERT INTO detalle_ae (id_esp_dae, id_abgd_dae) VALUES (:id_especialidad, :id_abogado)");
        $guardar_detalle->execute([
            ":id_abogado" => $id_abogado,
            ":id_especialidad" => $id_especialidad
        ]);
    }
}

echo '<div class="notification is-info is-light">
        <strong>¡Registro exitoso!</strong><br>
        Las especialidades se asignaron correctamente al abogado, actualize la pestaña para verlo reflejado.
      </div>';
?>