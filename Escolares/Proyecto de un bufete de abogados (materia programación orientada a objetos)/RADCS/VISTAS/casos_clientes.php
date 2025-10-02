<?php
require_once "PHP/main.php"; // Asegúrate de que la conexión esté configurada correctamente

// Conexión a la base de datos
$conexion = conexion();

// Obtener el ID del cliente desde la sesión
$id_cliente = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

if ($id_cliente == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se especificó un cliente válido. Por favor, inicie sesión.
         </div>');
}

// Consulta para obtener los casos del cliente en sesión junto con el abogado asociado
$casos = $conexion->prepare("
    SELECT casos.Id_cs, casos.No_cs, casos.Desc_cs, 
           abogados.Nom_abgd, abogados.App_abgd, abogados.Apm_abgd 
    FROM casos
    INNER JOIN abogados ON casos.Abgd_id_cs = abogados.Id_abgd
    WHERE casos.Cli_id_cs = :id_cliente
");
$casos->execute([':id_cliente' => $id_cliente]);
$casos = $casos->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container is-fluid" style="margin-top: 20px;">
    <!-- Mensaje de bienvenida -->
    <h2 class="title">Sus casos</h2>

    <!-- Lista de casos -->
    <div class="accordion mt-6">
    <?php foreach ($casos as $caso): ?>
        <div class="accordion-item" style="margin-bottom: 15px;"> <!-- Espaciado entre casos -->
            <header class="accordion-header is-flex is-justify-content-space-between is-align-items-center">
                <button class="accordion-trigger button is-link is-light" style="width: 100%; text-align: left;" onclick="toggleAccordion(this)">
                <?php echo htmlspecialchars($caso['No_cs']); ?> - Abogado: <?php echo htmlspecialchars($caso['Nom_abgd'] . ' ' . $caso['App_abgd'] . ' ' . $caso['Apm_abgd']); ?>
                </button>
            </header>
            <div class="accordion-content" style="display: none; padding: 10px; border: 1px solid #ddd; border-top: none;">
                <div>
                    <p class="description"><strong>Descripción:</strong> <?php echo htmlspecialchars($caso['Desc_cs']); ?></p>
                    <h3 class="title is-5">Reportes asociados:</h3>
                    <?php
                    // Obtener los reportes asociados al caso
                    $reportes_query = $conexion->prepare("SELECT * FROM reportes WHERE cs_rpt = :id_caso");
                    $reportes_query->execute([":id_caso" => $caso['Id_cs']]);
                    $reportes = $reportes_query->fetchAll();

                    if (count($reportes) > 0) {
                        foreach ($reportes as $reporte) {
                            echo '
                                <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                                    <p><strong>Título:</strong> ' . htmlspecialchars($reporte['Tt_rpt']) . '</p>
                                    <p class="reporte-descripcion"><strong>Descripción:</strong> ' . htmlspecialchars($reporte['Desc_rpt']) . '</p>
                                    <p><strong>Fecha:</strong> ' . htmlspecialchars($reporte['Fecha_rpt']) . '</p>
                                    <p><strong>Documentos:</strong></p>
                                    <ul>
                                        ' . (!empty($reporte['Doc1_rpt']) ? '<li><a href="PHP/descargar_documento.php?id=' . $reporte['Id_rpt'] . '&doc=Doc1_rpt" target="_blank">' . htmlspecialchars($reporte['Doc1_nombre_original']) . '</a></li>' : '') . '
                                        ' . (!empty($reporte['Doc2_rpt']) ? '<li><a href="PHP/descargar_documento.php?id=' . $reporte['Id_rpt'] . '&doc=Doc2_rpt" target="_blank">' . htmlspecialchars($reporte['Doc2_nombre_original']) . '</a></li>' : '') . '
                                        ' . (!empty($reporte['Doc3_rpt']) ? '<li><a href="PHP/descargar_documento.php?id=' . $reporte['Id_rpt'] . '&doc=Doc3_rpt" target="_blank">' . htmlspecialchars($reporte['Doc3_nombre_original']) . '</a></li>' : '') . '
                                    </ul>
                                </div>
                            ';
                        }
                    } else {
                        echo '<p>No hay reportes asociados a este caso.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div></div>
<script>
    // Función para alternar el acordeón
    function toggleAccordion(button) {
        const content = button.parentElement.nextElementSibling;
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
        } else {
            content.style.display = "none";
        }
    }
</script>