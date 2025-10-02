<?php
require_once "PHP/main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

$conexion = conexion();
if (!$conexion) {
    die("Error al conectar a la base de datos");
}

// Obtener el ID del abogado en sesión
$id_abogado = $_SESSION['id'];

// Obtener los clientes asociados al abogado en sesión con registros en la tabla `pld`
$clientes = $conexion->prepare("
    SELECT DISTINCT c.Id_cli, c.Nom_cli, c.App_cli, c.Apm_cli 
    FROM clientes c
    INNER JOIN pld p ON c.Id_cli = p.Id_cli_pld
    WHERE p.Id_abgd_pld = :id_abogado
");
$clientes->execute([':id_abogado' => $id_abogado]);
$clientes = $clientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos los clientes para el modal (sin filtrar por registros en `pld`)
$clientes_modal = $conexion->query("
    SELECT Id_cli, Nom_cli, App_cli, Apm_cli 
    FROM clientes
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container is-fluid" style="margin-top: 20px;">
    <h2 class="title">PLD</h2>

    <!-- Contenedor para botones y barra de búsqueda -->
    <div class="is-flex is-align-items-center" style="gap: 10px; margin-top: 10px;">
        <!-- Botón para abrir el modal -->
        <button class="button is-success" id="openModal">Agregar Registro</button>
    </div>

    <div class="form-rest mb-6 mt-6"></div>

    <!-- Modal para agregar registro -->
    <div class="modal" id="modalForm">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Registrar Pago</p>
                <button class="delete" aria-label="close" id="closeModal"></button>
            </header>
            <section class="modal-card-body">
                <!-- Formulario para agregar registro -->
                <form action="PHP/pld_guardar.php" method="POST" autocomplete="off" class="FormularioAjax">
                    <input type="hidden" name="Id_abgd" id="modal-id-abogado" value="<?php echo $_SESSION['id']; ?>">

                    <div class="field">
                        <label class="label">Cliente</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="Id_cli" required>
                                    <option value="" disabled selected>Seleccione un cliente</option>
                                    <?php
                                    foreach ($clientes_modal as $cliente) {
                                        echo '<option value="' . $cliente['Id_cli'] . '">' . $cliente['Nom_cli'] . ' ' . $cliente['App_cli'] . ' ' . $cliente['Apm_cli'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Descripción</label>
                        <div class="control">
                            <textarea name="Descripcion" class="textarea" placeholder="Ingrese la descripción" required></textarea>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Importe</label>
                        <div class="control">
                            <input type="number" name="Importe" class="input" step="0.01" placeholder="Ingrese el importe" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Fecha del pago</label>
                        <div class="control">
                            <input type="date" name="Fecha_pago" class="input" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Importe pagado</label>
                        <div class="control">
                            <input type="number" name="Importe_pagado" class="input" step="0.01" placeholder="Ingrese el importe pagado" required>
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
</div>

<div class="container is-fluid" style="margin-top: 20px;">
    <div class="accordion mt-6">
        <?php foreach ($clientes as $cliente): ?>
            <div class="accordion-item" style="margin-bottom: 15px;">
                <header class="accordion-header is-flex is-justify-content-space-between is-align-items-center">
                    <button class="accordion-trigger button is-link is-light" style="width: 100%; text-align: left;" onclick="toggleAccordion(this)">
                        <?php echo htmlspecialchars($cliente['Nom_cli'] . ' ' . $cliente['App_cli'] . ' ' . $cliente['Apm_cli']); ?>
                    </button>
                </header>
                <div class="accordion-content" style="display: none; padding: 10px; border: 1px solid #ddd; border-top: none;">
                    <h3 class="title is-5">Registros de Pagos</h3>
                    <form action="index.php?vista=facturar" method="POST">
                        <!-- Enviar el ID del cliente y del abogado -->
                        <input type="hidden" name="Id_cli" value="<?php echo $cliente['Id_cli']; ?>">
                        <input type="hidden" name="Id_abgd" value="<?php echo $_SESSION['id']; ?>">
                        <table class="table is-fullwidth is-striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="select-all-client"></th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Importe</th>
                                    <th>Fecha del Pago</th>
                                    <th>Importe Pagado</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Obtener los registros de pagos asociados al cliente
                                $registros = $conexion->prepare("
                                    SELECT Id_pld, Fe_pld, Desc_pld, Fepg_pld, Im_pld, Ipg_pld, Edo_pld, Tot_pld, Id_cli_pld, Id_abgd_pld
                                    FROM pld 
                                    WHERE Id_cli_pld = :id_cliente AND Id_abgd_pld = :id_abogado
                                ");
                                $registros->execute([
                                    ":id_cliente" => $cliente['Id_cli'],
                                    ":id_abogado" => $id_abogado
                                ]);
                                $pagos = $registros->fetchAll(PDO::FETCH_ASSOC);

                                if (count($pagos) > 0) {
                                    foreach ($pagos as $pago) {
                                        echo "<tr>";
                                        echo "<td><input type='checkbox' name='pagos[]' value='" . $pago['Id_pld'] . "' class='checkbox-client'></td>";
                                        echo "<td>" . htmlspecialchars($pago['Fe_pld']) . "</td>";
                                        echo "<td style='white-space: normal;'>" . htmlspecialchars($pago['Desc_pld']) . "</td>";
                                        echo "<td>$" . number_format($pago['Im_pld'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($pago['Fepg_pld']) . "</td>";
                                        echo "<td>$" . number_format($pago['Ipg_pld'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($pago['Edo_pld']) . "</td>";
                                        echo "<td>$" . number_format($pago['Tot_pld'], 2) . "</td>";
                                        echo "<td>
                                                <button type='button' class='button is-small is-warning' onclick='openEditModal(" . json_encode($pago) . ")'>
                                                    Editar
                                                </button>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No hay registros de pagos para este cliente.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="field">
                            <button type="submit" class="button is-primary" disabled>Facturar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal" id="modalEditForm">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Editar Pago</p>
                <button class="delete" aria-label="close" id="closeEditModal"></button>
            </header>
            <section class="modal-card-body">
                <form action="PHP/pld_actualizar.php" method="POST" autocomplete="off" class="FormularioAjax">
                    <!-- Campos ocultos para datos no editables -->
                    <input type="hidden" name="Id_pld" id="edit-id-pld">
                    <input type="hidden" name="Fe_pld" id="edit-fecha-creacion">
                    <input type="hidden" name="Edo_pld" id="edit-estado">
                    <input type="hidden" name="Tot_pld" id="edit-total">
                    <input type="hidden" name="Id_cli_pld" id="edit-id-cliente">
                    <input type="hidden" name="Id_abgd_pld" id="edit-id-abogado">

                    <div class="field">
                        <label class="label">Descripción</label>
                        <div class="control">
                            <textarea name="Desc_pld" id="edit-descripcion" class="textarea" required></textarea>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Importe</label>
                        <div class="control">
                            <input type="number" name="Im_pld" id="edit-importe" class="input" step="0.01" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Fecha del pago</label>
                        <div class="control">
                            <input type="date" name="Fepg_pld" id="edit-fecha-pago" class="input" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Importe pagado</label>
                        <div class="control">
                            <input type="number" name="Ipg_pld" id="edit-importe-pagado" class="input" step="0.01" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-warning">Actualizar</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>


<script>
    document.getElementById('openModal').addEventListener('click', function () {
        document.getElementById('modalForm').classList.add('is-active');
    });

    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('modalForm').classList.remove('is-active');
    });

    document.querySelector('.modal-background').addEventListener('click', function () {
        document.getElementById('modalForm').classList.remove('is-active');
    });

    function toggleAccordion(button) {
        const content = button.parentElement.nextElementSibling;
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
        } else {
            content.style.display = "none";
        }
    }

    function openEditModal(pago) {
        document.getElementById('edit-id-pld').value = pago.Id_pld;
        document.getElementById('edit-fecha-creacion').value = pago.Fe_pld;
        document.getElementById('edit-descripcion').value = pago.Desc_pld;
        document.getElementById('edit-importe').value = pago.Im_pld;
        document.getElementById('edit-fecha-pago').value = pago.Fepg_pld;
        document.getElementById('edit-importe-pagado').value = pago.Ipg_pld;
        document.getElementById('edit-estado').value = pago.Edo_pld;
        document.getElementById('edit-total').value = pago.Tot_pld;
        document.getElementById('edit-id-cliente').value = pago.Id_cli_pld;
        document.getElementById('edit-id-abogado').value = pago.Id_abgd_pld;

        document.getElementById('modalEditForm').classList.add('is-active');
    }

    document.getElementById('closeEditModal').addEventListener('click', function () {
        document.getElementById('modalEditForm').classList.remove('is-active');
    });

    document.querySelector('.modal-background').addEventListener('click', function () {
        document.getElementById('modalEditForm').classList.remove('is-active');
    });

    document.querySelectorAll('.select-all-client').forEach(selectAllCheckbox => {
        selectAllCheckbox.addEventListener('change', function () {
            const checkboxes = this.closest('table').querySelectorAll('.checkbox-client');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            toggleFacturarButton(this.closest('form'));
        });
    });

    document.querySelectorAll('.checkbox-client').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            toggleFacturarButton(this.closest('form'));
        });
    });

    function toggleFacturarButton(form) {
        const checkboxes = form.querySelectorAll('.checkbox-client');
        const facturarButton = form.querySelector('button[type="submit"]');
        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
        facturarButton.disabled = !anyChecked;
    }

    document.querySelectorAll('form').forEach(form => {
        toggleFacturarButton(form);
    });
    document.querySelector('#modalForm button[type="submit"]').disabled = false;

    document.querySelectorAll('.button.is-warning').forEach(editButton => {
    editButton.disabled = false;
});
</script>
