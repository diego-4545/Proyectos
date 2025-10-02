<?php
require_once "PHP/main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

$conexion = conexion();
if (!$conexion) {
    die("Error al conectar a la base de datos");
}

// Verificar clientes
$clientes = $conexion->query("SELECT Id_cli, Nom_cli, App_cli, Apm_cli FROM clientes");
if ($clientes->rowCount() == 0) {
    die("No se encontraron clientes en la base de datos");
}

?>

<div class="container is-fluid mb-6">
    <h1 class="title">Citas</h1>
    <h2 class="subtitle">Seleccione su especialidad, su abogado y genere su cita</h2>
    <div class="form-rest mb-6 mt-6"></div>
</div>
<div class="form-rest mb-6 mt-6"></div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php";
    ?>
    <div class="columns">
        <!-- Barra lateral de especialidades -->
        <div class="column is-one-quarter" style="background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
            <h2 class="title has-text-centered">Especialidades</h2>
            <?php
                $especialidades = conexion();
                $especialidades = $especialidades->query("SELECT * FROM especialidades");
                if ($especialidades->rowCount() > 0) {
                    $especialidades = $especialidades->fetchAll();
                    foreach ($especialidades as $row) {
                        echo '<a href="index.php?vista=cita&especialidad_id=' . $row['Id_esp'] . '" class="button is-link is-light is-fullwidth mb-2">' . $row['No_esp'] . '</a>';
                    }
                } else {
                    echo '<p class="has-text-centered">No hay especialidades registradas</p>';
                }
                $especialidades = null;
            ?>
        </div>

        <!-- Contenido principal -->
        <div class="column">
            <?php
                $especialidad_id = (isset($_GET['especialidad_id'])) ? $_GET['especialidad_id'] : 0;

                /*== Verificando especialidad ==*/
                $check_especialidad = conexion();
                $check_especialidad = $check_especialidad->query("SELECT * FROM especialidades WHERE Id_esp='$especialidad_id'");

                if ($check_especialidad->rowCount() > 0) {

                    $check_especialidad = $check_especialidad->fetch();

                    echo '
                        <h2 class="title has-text-centered">' . $check_especialidad['No_esp'] . '</h2>
                        <p class="has-text-centered pb-6"><strong>Descripción:</strong> ' . htmlspecialchars($check_especialidad['Desc_esp']) . '</p>
                        <p class="has-text-centered pb-6">Abogados asociados a esta especialidad:</p>
                    ';

                    require_once "./php/main.php";

                    # Obtener abogados #
                    $abogados_query = conexion();
                    $abogados_query = $abogados_query->prepare("
                        SELECT abogados.Id_abgd, abogados.Ced_abgd, abogados.Nom_abgd, abogados.App_abgd, abogados.Apm_abgd 
                        FROM detalle_ae
                        INNER JOIN abogados ON detalle_ae.id_abgd_dae = abogados.Id_abgd
                        WHERE detalle_ae.id_esp_dae = :especialidad_id
                    ");
                    $abogados_query->execute([":especialidad_id" => $especialidad_id]);
                    $abogados = $abogados_query->fetchAll();

                    if (count($abogados) > 0) {
                        foreach ($abogados as $abogado) {
                            echo '
                                <article class="media" style="padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; background-color: #f9f9f9;">
                                    <div class="media-content">
                                        <div class="content">
                                            <p style="font-size: 1.25rem;">
                                                <strong>' . htmlspecialchars($abogado['Nom_abgd'] . ' ' . $abogado['App_abgd'] . ' ' . $abogado['Apm_abgd']) . '</strong><br>
                                                <strong>CÉDULA:</strong> ' . htmlspecialchars($abogado['Ced_abgd']) . '
                                            </p>
                                        </div>
                                    </div>
                                    <div class="media-right">
                                        <button class="button is-primary is-rounded is-medium" onclick="openModal(\'' . htmlspecialchars($abogado['Nom_abgd'] . ' ' . $abogado['App_abgd'] . ' ' . $abogado['Apm_abgd']) . '\', \'' . $abogado['Id_abgd'] . '\')">Generar Cita</button>
                                    </div>
                                </article>
                            ';
                        }
                    } else {
                        echo '<p class="has-text-centered">No hay abogados asociados a esta especialidad.</p>';
                    }

                } else {
                    echo '<h2 class="has-text-centered title">Seleccione una especialidad para empezar</h2>';
                }
                $check_especialidad = null;
            ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-background" onclick="closeModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Detalles de la Cita</p>
            <button class="delete" aria-label="close" onclick="closeModal()"></button>
        </header>
        <section class="modal-card-body">
            <p><strong>Abogado:</strong> <span id="modal-abogado"></span></p>
            <form action="PHP/cita_guardar.php" method="POST" class="FormularioAjax" autocomplete="off">
                <!-- Campo oculto para enviar el ID del abogado -->
                <input type="hidden" name="Id_abgd" id="modal-id-abogado">

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
                <div class="field">
                    <label class="label">Fecha</label>
                    <div class="control">
                        <input type="date" name="fecha_cita" class="input" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Hora</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select name="hora_cita" required>
                                <option value="" disabled selected>Seleccione una hora</option>
                                <?php
                                // Generar opciones de hora desde las 9:00 hasta las 21:00 en intervalos de 30 minutos
                                for ($hour = 9; $hour <= 21; $hour++) {
                                    foreach (['00', '30'] as $minute) {
                                        $time = sprintf('%02d:%s', $hour, $minute);
                                        echo '<option value="' . $time . '">' . $time . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button type="submit" class="button is-success">Guardar</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

<script>
    function openModal(abogadoNombre, abogadoId) {
        const modal = document.getElementById('modal');
        document.getElementById('modal-abogado').textContent = abogadoNombre;
        document.getElementById('modal-id-abogado').value = abogadoId; // Asignar el ID del abogado al campo oculto
        modal.classList.add('is-active');
    }

    function closeModal() {
        const modal = document.getElementById('modal');
        modal.classList.remove('is-active');
    }
</script>