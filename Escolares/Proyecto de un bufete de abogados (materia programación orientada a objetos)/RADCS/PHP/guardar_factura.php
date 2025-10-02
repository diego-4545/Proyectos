<?php
// filepath: c:\xampp\htdocs\RADCS\PHP\guardar_factura.php

require_once "main.php"; // Asegúrate de que la ruta sea correcta

// Configurar el encabezado para recibir JSON
header("Content-Type: application/json");

try {
    // Leer los datos enviados desde la solicitud AJAX
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que los datos necesarios estén presentes
    if (!isset($data['id_cliente'], $data['pagos'], $data['fecha_hoy'], $data['total_factura']) || !is_array($data['pagos'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }

    // Limpiar los datos recibidos
    $id_cliente = limpiar_cadena($data['id_cliente']);
    $pagos = array_map('limpiar_cadena', $data['pagos']);
    $fecha_hoy = limpiar_cadena($data['fecha_hoy']);
    $total_factura = limpiar_cadena($data['total_factura']);

    // Conectar a la base de datos
    $conexion = conexion();
    if (!$conexion) {
        echo json_encode(['success' => false, 'message' => 'Error al conectar a la base de datos']);
        exit;
    }

    // Insertar cada pago como una fila separada en la tabla factura
    $guardar_factura = $conexion->prepare("
        INSERT INTO factura (Id_cli_fc, Id_pld_fc, fc_fc, tot_fc) 
        VALUES (:id_cliente, :id_pld_fc, :fecha_hoy, :total_factura)
    ");

    foreach ($pagos as $id_pld_fc) {
        $guardar_factura->execute([
            ':id_cliente' => $id_cliente,
            ':id_pld_fc' => $id_pld_fc,
            ':fecha_hoy' => $fecha_hoy,
            ':total_factura' => $total_factura
        ]);
    }

    // Responder con éxito
    echo json_encode(['success' => true, 'message' => 'Factura guardada correctamente']);
} catch (Exception $e) {
    // Manejar errores
    echo json_encode(['success' => false, 'message' => 'Error al guardar la factura: ' . $e->getMessage()]);
}
?>