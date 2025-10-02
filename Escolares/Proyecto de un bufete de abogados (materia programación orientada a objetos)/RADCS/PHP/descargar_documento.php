<?php
require_once "main.php"; // Asegúrate de que la conexión esté configurada correctamente

// Verificar si se pasaron los parámetros necesarios
if (isset($_GET['id']) && isset($_GET['doc'])) {
    $id_reporte = limpiar_cadena($_GET['id']);
    $campo_documento = limpiar_cadena($_GET['doc']);

    // Validar que el campo del documento sea válido
    $campos_validos = ['Doc1_rpt', 'Doc2_rpt', 'Doc3_rpt'];
    $campos_mime = ['Doc1_mime', 'Doc2_mime', 'Doc3_mime'];
    $campos_nombre = ['Doc1_nombre_original', 'Doc2_nombre_original', 'Doc3_nombre_original'];

    $indice = array_search($campo_documento, $campos_validos);
    if ($indice === false) {
        die("Campo de documento no válido.");
    }

    $campo_mime = $campos_mime[$indice];
    $campo_nombre = $campos_nombre[$indice];

    // Conexión a la base de datos
    $conexion = conexion();

    // Consultar el contenido del documento, tipo MIME y nombre original
    $query = $conexion->prepare("
        SELECT $campo_documento AS contenido, $campo_mime AS mime_type, $campo_nombre AS nombre_original 
        FROM reportes 
        WHERE Id_rpt = :id_reporte
    ");
    $query->execute([":id_reporte" => $id_reporte]);
    $resultado = $query->fetch(PDO::FETCH_ASSOC);

    if ($resultado && !empty($resultado['contenido'])) {
        // Obtener el contenido binario, tipo MIME y nombre original
        $contenido = $resultado['contenido'];
        $tipo_mime = $resultado['mime_type'] ?? "application/octet-stream"; // Tipo MIME genérico si no está definido
        $nombre_archivo = $resultado['nombre_original'] ?? "documento_" . uniqid(); // Nombre genérico si no está definido

        // Enviar el archivo al navegador
        header("Content-Type: $tipo_mime");
        header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
        header("Content-Length: " . strlen($contenido));
        echo $contenido;
        exit;
    } else {
        die("Documento no encontrado.");
    }
} else {
    die("Parámetros inválidos.");
}
?>