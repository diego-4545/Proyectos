<?php
require_once "PHP/main.php"; // Asegúrate de que la conexión esté configurada correctamente

// Conexión a la base de datos
$conexion = conexion();

// Obtener el ID del abogado en sesión
$id_abogado = $_SESSION['id'];

// Consulta para obtener los casos del abogado en sesión
$casos = $conexion->prepare("
    SELECT casos.Id_cs, casos.No_cs, casos.Desc_cs, clientes.Nom_cli, clientes.App_cli, clientes.Apm_cli 
    FROM casos
    INNER JOIN clientes ON casos.Cli_id_cs = clientes.Id_cli
    WHERE casos.abgd_id_cs = :id_abogado
");
$casos->execute([':id_abogado' => $id_abogado]);
$casos = $casos->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los clientes
$clientes = $conexion->query("
    SELECT Id_cli, Nom_cli, App_cli, Apm_cli 
    FROM clientes
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container is-fluid" style="margin-top: 20px;">
    <!-- Mensaje de bienvenida -->
    <h2 class="title">Sus casos</h2>

    <!-- Botón para abrir el modal -->
    <button class="button is-success" id="btn-abrir-modal">Crear un nuevo caso</button>
    <div class="form-rest mb-6 mt-6"></div>

    <!-- Modal para crear un nuevo caso -->
    <div id="modal" class="modal">
        <div class="modal-background" onclick="closeModal()"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Crear un nuevo caso</p>
                <button class="delete" aria-label="close" onclick="closeModal()"></button>
            </header>
            <section class="modal-card-body">
                <form action="PHP/caso_guardar.php" method="POST" class="FormularioAjax" autocomplete="off">
                    <!-- Campo oculto para enviar el ID del abogado -->
                    <input type="hidden" name="Id_abgd" id="modal-id-abogado" value="<?php echo $_SESSION['id']; ?>">

                    <!-- Selección del cliente -->
                    <div class="field">
                        <label class="label">Cliente</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="Id_cli" required>
                                    <option value="" disabled selected>Seleccione un cliente</option>
                                    <?php
                                    foreach ($clientes as $cliente) {
                                        echo '<option value="' . $cliente['Id_cli'] . '">' . $cliente['Nom_cli'] . ' ' . $cliente['App_cli'] . ' ' . $cliente['Apm_cli'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Nombre del caso -->
                    <div class="field">
                        <label class="label">Nombre del caso</label>
                        <div class="control">
                            <input type="text" name="nombre_caso" class="input" placeholder="Ingrese el nombre del caso" required>
                        </div>
                    </div>

                    <!-- Descripción del caso -->
                    <div class="field">
                        <label class="label">Descripción</label>
                        <div class="control">
                            <textarea name="descripcion_caso" class="textarea" placeholder="Ingrese una descripción del caso" required></textarea>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="field is-grouped">
                        <div class="control">
                            <button type="submit" class="button is-success">Guardar</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>



    <!-- Lista de casos -->
    <div class="accordion mt-6">
    <?php foreach ($casos as $caso): ?>
        <div class="accordion-item" style="margin-bottom: 15px;"> <!-- Espaciado entre casos -->
            <header class="accordion-header is-flex is-justify-content-space-between is-align-items-center">
                <button class="accordion-trigger button is-link is-light" style="width: 100%; text-align: left;" onclick="toggleAccordion(this)">
                    <?php echo htmlspecialchars($caso['No_cs']); ?> - Cliente: <?php echo htmlspecialchars($caso['Nom_cli'] . ' ' . $caso['App_cli'] . ' ' . $caso['Apm_cli']); ?>
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
                    <!-- Botón para agregar reporte -->
                    <div style="margin-top: 20px; text-align: right;">
                        <button class="button is-info is-small" onclick="openReporteModal(<?php echo $caso['Id_cs']; ?>)">Agregar reporte</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

    <!-- Modal para agregar reporte -->
    <div id="modal-reporte" class="modal">
        <div class="modal-background" onclick="closeReporteModal()"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Agregar reporte</p>
                <button class="delete" aria-label="close" onclick="closeReporteModal()"></button>
            </header>
            <section class="modal-card-body">
                <form action="PHP/reporte_guardar.php" method="POST" class="FormularioAjax" enctype="multipart/form-data" autocomplete="off">
                    <!-- Campo oculto para enviar el ID del caso -->
                    <input type="hidden" name="Id_caso" id="modal-reporte-id-caso">

                    <!-- Título del reporte -->
                    <div class="field">
                        <label class="label">Título del reporte</label>
                        <div class="control">
                            <input type="text" name="titulo_reporte" class="input" placeholder="Ingrese el título del reporte" required>
                        </div>
                    </div>

                    <!-- Descripción del reporte -->
                    <div class="field">
                        <label class="label">Descripción del reporte</label>
                        <div class="control">
                            <textarea name="descripcion_reporte" class="textarea" placeholder="Ingrese una descripción del reporte" required></textarea>
                        </div>
                    </div>

                    <!-- Subir documentos -->
                    <div class="field">
                        <label class="label">Documentos</label>
                        <div class="control">
                            <input type="file" name="documento_1" class="input mb-2">
                            <input type="file" name="documento_2" class="input mb-2">
                            <input type="file" name="documento_3" class="input">
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="field is-grouped">
                        <div class="control">
                            <button type="submit" class="button is-success">Guardar</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

</div></div>

<style>
    .description {
        word-wrap: break-word;
        overflow: auto;
        max-height: none;
        line-height: 1.5;
        display: block;
        padding: 5px;
        box-sizing: border-box;
    }

    .reporte-descripcion {
        word-wrap: break-word;
        overflow: auto;
        max-height: none;
        line-height: 1.5;
        display: block;
        box-sizing: border-box;
    }
</style>

<script>
    // Función para cerrar el modal de crear caso
    function closeModal() {
        document.getElementById('modal').classList.remove('is-active');
    }

    // Función para abrir el modal de crear caso
    document.getElementById('btn-abrir-modal').addEventListener('click', function () {
        document.getElementById('modal').classList.add('is-active');
    });

    // Función para alternar el acordeón
    function toggleAccordion(button) {
        const content = button.parentElement.nextElementSibling;
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
        } else {
            content.style.display = "none";
        }
    }

    // Función para abrir el modal de agregar reporte
    function openReporteModal(casoId) {
        document.getElementById('modal-reporte-id-caso').value = casoId;
        document.getElementById('modal-reporte').classList.add('is-active');
    }

    // Función para cerrar el modal de agregar reporte
    function closeReporteModal() {
        document.getElementById('modal-reporte').classList.remove('is-active');
    }
</script>