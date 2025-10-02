<?php
require_once "PHP/main.php"; // Asegúrate de que la ruta sea correcta

// Verificar que se recibieron los datos necesarios
if (isset($_POST['Id_abgd'], $_POST['Id_cli'], $_POST['pagos']) && is_array($_POST['pagos'])) {
    $id_abogado = limpiar_cadena($_POST['Id_abgd']);
    $id_cliente = limpiar_cadena($_POST['Id_cli']);
    $pagos = array_map('limpiar_cadena', $_POST['pagos']); // Limpiar los IDs de los pagos

    // Conectar a la base de datos
    $conexion = conexion();
    if (!$conexion) {
        die("Error al conectar a la base de datos");
    }

    // Obtener la fecha de hoy con formato completo
    $fecha_hoy = date('Y-m-d'); 

    // Obtener el último ID de factura en la base de datos
    $consulta_ultimo_id = $conexion->query("SELECT MAX(Id_fc) AS ultimo_id FROM factura");
    $resultado_ultimo_id = $consulta_ultimo_id->fetch(PDO::FETCH_ASSOC);
    $ultimo_id_factura = $resultado_ultimo_id['ultimo_id'] ? $resultado_ultimo_id['ultimo_id'] + 1 : 1; // Si no hay facturas, empieza en 1

    // Obtener los datos del cliente
    $cliente = $conexion->prepare("
        SELECT Nom_cli, App_cli, Apm_cli, Dir_cli, Cp_cli, Rf_cli, Tel_cli 
        FROM clientes 
        WHERE Id_cli = :id_cliente
    ");
    $cliente->execute([':id_cliente' => $id_cliente]);
    $cliente = $cliente->fetch(PDO::FETCH_ASSOC);

    // Obtener los datos de los pagos seleccionados
    $placeholders = implode(',', array_fill(0, count($pagos), '?'));
    $consulta_pagos = $conexion->prepare("
        SELECT Id_pld, Fe_pld, Desc_pld, Im_pld, Fepg_pld, Ipg_pld, Edo_pld, Tot_pld 
        FROM pld 
        WHERE Id_pld IN ($placeholders)
    ");
    $consulta_pagos->execute($pagos);
    $pagos_seleccionados = $consulta_pagos->fetchAll(PDO::FETCH_ASSOC);

    // Calcular el total de la factura
    $total_factura = array_sum(array_column($pagos_seleccionados, 'Tot_pld'));

    // Generar la factura (sin guardar nada en la base de datos)
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
    echo "<p style='font-size: 14px;'>Número de factura: <strong>" . htmlspecialchars($ultimo_id_factura) . "</strong></p>";
    echo "<p style='font-size: 14px;'>Fecha: <strong>" . htmlspecialchars($fecha_hoy) . "</strong></p>";
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

    foreach ($pagos_seleccionados as $pago) {
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

    // Botones para guardar y regresar
    echo "<div style='text-align: center; margin-top: 30px;'>";
    echo "<button onclick='guardarFactura()' style='text-decoration: none; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; font-size: 16px; border: none; cursor: pointer;'>Guardar y Descargar</button>";
    echo "<a href='index.php?vista=pld' style='text-decoration: none; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; font-size: 16px; margin-left: 10px;'>Regresar a PLD</a>";
    echo "</div>";

    echo "</div>";
}
?>

<script>
    function guardarFactura() {
    const facturaData = {
        id_cliente: <?php echo json_encode($id_cliente); ?>,
        pagos: <?php echo json_encode($pagos); ?>,
        fecha_hoy: <?php echo json_encode($fecha_hoy); ?>,
        total_factura: <?php echo json_encode($total_factura); ?>
    };

    console.log(facturaData); // Verifica los datos en la consola del navegador

    fetch('PHP/guardar_factura.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(facturaData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Factura guardada correctamente.');
            const factura = document.getElementById('factura');
            const ventana = window.open('', '_blank');
            ventana.document.write('<html><head><title>Factura</title></head><body>');
            ventana.document.write(factura.outerHTML);
            ventana.document.write('</body></html>');
            ventana.document.close();
            ventana.print();
        } else {
            alert('Error al guardar la factura: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
