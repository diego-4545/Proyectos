<?php
require_once "main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

/*== Almacenando datos del formulario ==*/
$id_caso = limpiar_cadena($_POST['Id_caso']);
$titulo_reporte = limpiar_cadena($_POST['titulo_reporte']);
$descripcion_reporte = limpiar_cadena($_POST['descripcion_reporte']);

/*== Verificar conexión a la base de datos ==*/
$conexion = conexion();
if (!$conexion) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo conectar a la base de datos.
         </div>');
}

/*== Validar que el caso existe ==*/
$check_caso = $conexion->query("SELECT Id_cs FROM casos WHERE Id_cs='$id_caso'");
if ($check_caso->rowCount() == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            El caso seleccionado no existe.
         </div>');
}

/*== Manejo de documentos subidos como binarios ==*/
$documentos = [];
$tipos_mime = [];
$nombres_originales = [];

for ($i = 1; $i <= 3; $i++) {
    $campo = "documento_$i";
    if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] == 0) {
        // Leer el contenido del archivo como binario
        $contenido_binario = file_get_contents($_FILES[$campo]['tmp_name']);
        $documentos[] = $contenido_binario;

        // Capturar el tipo MIME y el nombre original
        $tipos_mime[] = $_FILES[$campo]['type'];
        $nombres_originales[] = $_FILES[$campo]['name'];
    } else {
        $documentos[] = null; // Si no se envió el archivo, se almacena como null
        $tipos_mime[] = null;
        $nombres_originales[] = null;
    }
}

/*== Registrar el reporte en la base de datos ==*/
$guardar_reporte = $conexion->prepare("
    INSERT INTO reportes (Tt_rpt, Desc_rpt, Doc1_rpt, Doc2_rpt, Doc3_rpt, Doc1_mime, Doc2_mime, Doc3_mime, Doc1_nombre_original, Doc2_nombre_original, Doc3_nombre_original, cs_rpt, Fecha_rpt) 
    VALUES (:titulo_reporte, :descripcion_reporte, :doc1, :doc2, :doc3, :mime1, :mime2, :mime3, :nombre1, :nombre2, :nombre3, :id_caso, :fecha_reporte)
");
$guardar_reporte->execute([
    ":titulo_reporte" => $titulo_reporte,
    ":descripcion_reporte" => $descripcion_reporte,
    ":doc1" => $documentos[0],
    ":doc2" => $documentos[1],
    ":doc3" => $documentos[2],
    ":mime1" => $tipos_mime[0],
    ":mime2" => $tipos_mime[1],
    ":mime3" => $tipos_mime[2],
    ":nombre1" => $nombres_originales[0],
    ":nombre2" => $nombres_originales[1],
    ":nombre3" => $nombres_originales[2],
    ":id_caso" => $id_caso,
    ":fecha_reporte" => date("Y-m-d") // Fecha y hora actual
]);

if ($guardar_reporte->rowCount() > 0) {
    echo '<div class="notification is-info is-light">
            <strong>¡Reporte registrado!</strong><br>
            El reporte se registró correctamente, actualize la pestaña para verlo reflejado.
          </div>';
} else {
    echo '<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se pudo registrar el reporte. Intente nuevamente.
          </div>';
}
?>