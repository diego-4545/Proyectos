<?php
require_once "PHP/main.php"; // Asegúrate de que la conexión esté configurada correctamente

// Verificar que se recibió el ID de la factura
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_factura = limpiar_cadena($_GET['id']);

    // Conectar a la base de datos
    $conexion = conexion();
    if (!$conexion) {
        die("Error al conectar a la base de datos");
    }

    // Obtener los datos de la factura seleccionada
    $factura_query = $conexion->prepare("
        SELECT fc_fc, tot_fc, Id_cli_fc 
        FROM factura 
        WHERE Id_fc = :id_factura
    ");
    $factura_query->execute([':id_factura' => $id_factura]);
    $factura = $factura_query->fetch(PDO::FETCH_ASSOC);

    if (!$factura) {
        die("<div class='notification is-danger is-light'>Factura no encontrada.</div>");
    }

    // Obtener todos los pagos que compartan la misma fecha y total
    $pagos_query = $conexion->prepare("
        SELECT pld.Fe_pld, pld.Desc_pld, pld.Im_pld, pld.Fepg_pld, pld.Ipg_pld, pld.Edo_pld, pld.Tot_pld 
        FROM factura 
        INNER JOIN pld ON factura.Id_pld_fc = pld.Id_pld 
        WHERE factura.fc_fc = :fecha AND factura.tot_fc = :total
    ");
    $pagos_query->execute([
        ':fecha' => $factura['fc_fc'],
        ':total' => $factura['tot_fc']
    ]);
    $pagos = $pagos_query->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los datos del cliente
    $cliente_query = $conexion->prepare("
        SELECT Nom_cli, App_cli, Apm_cli, Dir_cli, Cp_cli, Rf_cli, Tel_cli 
        FROM clientes 
        WHERE Id_cli = :id_cliente
    ");
    $cliente_query->execute([':id_cliente' => $factura['Id_cli_fc']]);
    $cliente = $cliente_query->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        die("<div class='notification is-danger is-light'>Cliente no encontrado.</div>");
    }

    // Calcular el total de la factura
    $total_factura = $factura['tot_fc'];

    // Generar la factura
    echo "<div id='factura' style='font-family: Arial, sans-serif; margin: 0 auto; width: 210mm; height: 297mm; border: 1px solid #ddd; padding: 20px; border-radius: 10px; box-sizing: border-box;'>";

    // Encabezado de la empresa con logo
    echo "<div style='text-align: center; margin-bottom: 20px;'>";
    echo "<img src='./IMG/Logo.avif' alt='Logo' style='max-width: 150px; margin-bottom: 10px;'>";
    echo "<h1 style='color: #4CAF50;'>Soluciones Creativas</h1>";
    echo "<p>Los Soles #133, Col. Mirador del Campestre</p>";
    echo "<p>San Pedro Garza García, Nuevo León, CP. 66266</p>";
    echo "<p>Tel. +52 (81) 1366 2300</p>";
    echo "</div>";

    // Información de la factura
    echo "<div style='text-align: center; margin-bottom: 20px;'>";
    echo "<h2 style='color: #4CAF50;'>FACTURA</h2>";
    echo "<p style='font-size: 14px;'>Número de factura: <strong>" . htmlspecialchars($id_factura) . "</strong></p>";
    echo "<p style='font-size: 14px;'>Fecha: <strong>" . htmlspecialchars($factura['fc_fc']) . "</strong></p>";
    echo "</div>";

    // Información del cliente
    echo "<div style='display: flex; justify-content: space-between; margin-bottom: 20px;'>";
    echo "<div>";
    echo "<h3 style='background-color: #4CAF50; color: white; padding: 5px;'>Facturar a:</h3>";
    echo "<p><strong>" . htmlspecialchars($cliente['Nom_cli'] . ' ' . $cliente['App_cli'] . ' ' . $cliente['Apm_cli']) . "</strong></p>";
    echo "<p>Dirección: " . htmlspecialchars($cliente['Dir_cli']) . "</p>";
    echo "<p>Código Postal: " . htmlspecialchars($cliente['Cp_cli']) . "</p>";
    echo "<p>RFC: " . htmlspecialchars($cliente['Rf_cli']) . "</p>";
    echo "<p>Teléfono: " . htmlspecialchars($cliente['Tel_cli']) . "</p>";
    echo "</div>";
    echo "</div>";

    // Detalles de los pagos
    echo "<h3 style='background-color: #4CAF50; color: white; padding: 5px;'>Detalles de los pagos</h3>";
    echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
    echo "<thead style='background-color: #f2f2f2;'>";
    echo "<tr>";
    echo "<th>Fecha</th>";
    echo "<th>Descripción</th>";
    echo "<th>Importe</th>";
    echo "<th>Fecha del Pago</th>";
    echo "<th>Importe Pagado</th>";
    echo "<th>Estado</th>";
    echo "<th>Total</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($pagos as $pago) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($pago['Fe_pld']) . "</td>";
        echo "<td>" . htmlspecialchars($pago['Desc_pld']) . "</td>";
        echo "<td style='text-align: right;'>$" . number_format($pago['Im_pld'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($pago['Fepg_pld']) . "</td>";
        echo "<td style='text-align: right;'>$" . number_format($pago['Ipg_pld'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($pago['Edo_pld']) . "</td>";
        echo "<td style='text-align: right;'>$" . number_format($pago['Tot_pld'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    // Totales
    echo "<div style='margin-top: 20px; text-align: right;'>";
    echo "<p style='font-size: 18px;'><strong>Total:</strong> $" . number_format($total_factura, 2) . "</p>";
    echo "</div>";

    // Botones para descargar y regresar
    echo "<div style='text-align: center; margin-top: 30px;'>";
    echo "<button onclick='descargarFactura()' style='text-decoration: none; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; font-size: 16px; border: none; cursor: pointer;'>Descargar</button>";
    echo "<a href='index.php?vista=facturas' style='text-decoration: none; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; font-size: 16px; margin-left: 10px;'>Regresar</a>";
    echo "</div>";

    echo "</div>";
} else {
    echo "<div class='notification is-danger is-light'>No se recibió un ID de factura válido.</div>";
}
?>

<script>
    function descargarFactura() {
        const factura = document.getElementById('factura');
        const ventana = window.open('', '_blank');
        ventana.document.write('<html><head><title>Factura</title></head><body>');
        ventana.document.write(factura.outerHTML);
        ventana.document.write('</body></html>');
        ventana.document.close();
        ventana.print();
    }
</script>