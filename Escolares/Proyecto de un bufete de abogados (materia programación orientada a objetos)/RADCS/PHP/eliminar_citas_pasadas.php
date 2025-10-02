<?php
require_once "main.php"; // Asegúrate de que la conexión esté configurada correctamente

$conexion = conexion();
if (!$conexion) {
    die("Error al conectar a la base de datos");
}

try {
    // Consulta para eliminar citas cuya fecha sea anterior a hoy
    $query = $conexion->prepare("
        DELETE FROM cita 
        WHERE dia_ct < CURDATE()
    ");

    $query->execute();

    // Verificar cuántas filas fueron afectadas
    $filas_eliminadas = $query->rowCount();

    echo "<div class='notification is-info is-light'>
            <strong>¡Operación exitosa!</strong><br>
            Se eliminaron $filas_eliminadas citas cuya fecha era anterior a hoy.
          </div>";
} catch (PDOException $e) {
    echo "<div class='notification is-danger is-light'>
            <strong>¡Error!</strong><br>
            No se pudieron eliminar las citas: " . $e->getMessage() . "
          </div>";
}
?>