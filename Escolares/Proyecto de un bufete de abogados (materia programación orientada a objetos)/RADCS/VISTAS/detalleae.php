<?php
require_once "PHP/main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

$conexion = conexion();
if (!$conexion) {
    die("Error al conectar a la base de datos");
}

// Verificar abogados
$abogados = $conexion->query("SELECT Id_abgd, Ced_abgd, Nom_abgd, App_abgd, Apm_abgd FROM abogados");
if ($abogados->rowCount() == 0) {
    die("No se encontraron abogados en la base de datos");
}

// Verificar especialidades
$especialidades = $conexion->query("SELECT Id_esp, No_esp FROM especialidades");
if ($especialidades->rowCount() == 0) {
    die("No se encontraron especialidades en la base de datos");
}
?>

<div class="container is-fluid" style="margin-top: 20px;">
  <h2 class="title">Abogados y sus especialidades</h2>

  <!-- Contenedor para botones y barra de búsqueda -->
  <div class="is-flex is-align-items-center" style="gap: 10px; margin-top: 10px;">
    <!-- Botón para abrir el modal -->
    <button class="button is-success" id="openModal">Agregar Abogado con Especialidades</button>

    <!-- Barra de búsqueda -->
    <form action="index.php?vista=detalleae" method="GET" class="is-flex" style="flex-grow: 1;">
      <input type="hidden" name="vista" value="detalleae">
      <div class="field has-addons" style="width: 100%;">
        <div class="control" style="flex-grow: 1;">
          <input class="input" type="text" name="busqueda" placeholder="Buscar por cédula, nombre o apellidos" value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>">
        </div>
        <div class="control">
          <button class="button is-info" type="submit">Buscar</button>
        </div>
      </div>
    </form>

    <!-- Botón de regresar -->
    <a href="http://localhost/RADCS/index.php?vista=detalleae" class="button is-warning">Regresar</a>
  </div>

  <div class="form-rest mb-6 mt-6"></div>

  <!-- Modal -->
  <div class="modal" id="modalForm">
    <div class="modal-background"></div>
    <div class="modal-card">
      <header class="modal-card-head">
        <p class="modal-card-title">Registrar Abogado con Especialidades</p>
        <button class="delete" aria-label="close" id="closeModal"></button>
      </header>
      <section class="modal-card-body">
        <!-- Formulario para agregar abogado con especialidades -->
        <form action="PHP/detalleae_guardar.php" method="POST" class="FormularioAjax" autocomplete="off">
          <div class="field">
              <label class="label">Abogado</label>
              <div class="control">
                  <div class="select is-fullwidth">
                      <select name="Id_abgd" required>
                          <option value="" disabled selected>Seleccione un abogado</option>
                          <?php
                          foreach ($abogados as $abogado) {
                              echo '<option value="' . $abogado['Id_abgd'] . '">' . $abogado['Ced_abgd'] . ' - ' . $abogado['Nom_abgd'] . ' ' . $abogado['App_abgd'] . ' ' . $abogado['Apm_abgd'] . '</option>';
                          }
                          ?>
                      </select>
                  </div>
              </div>
          </div>
          <div class="field">
              <label class="label">Especialidades</label>
              <div class="control">
                  <div class="select is-multiple is-fullwidth">
                      <select name="Id_esp[]" multiple required>
                          <?php
                          foreach ($especialidades as $especialidad) {
                              echo '<option value="' . $especialidad['Id_esp'] . '">' . $especialidad['No_esp'] . '</option>';
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
</div>

<div class="container pb-6 pt-6">  
    <?php
        require_once "PHP/main.php";

        # Eliminar usuario #
        if (isset($_GET['user_id_del'])) {
            require_once "PHP/detalleae_eliminar.php";
        }

        if (!isset($_GET['page'])) {
            $pagina = 1;
        } else {
            $pagina = (int) $_GET['page'];
            if ($pagina <= 1) {
                $pagina = 1;
            }
        }

        $pagina = limpiar_cadena($pagina);
        $url = "index.php?vista=detalleae&page=";
        $registros = 10;
        $busqueda = isset($_GET['busqueda']) ? limpiar_cadena($_GET['busqueda']) : "";

        # Paginador usuario #
        require_once "PHP/detalleae_lista.php";
    ?>
</div>

<script>
  // Abrir el modal
  document.getElementById('openModal').addEventListener('click', function() {
      document.getElementById('modalForm').classList.add('is-active');
  });

  // Cerrar el modal
  document.getElementById('closeModal').addEventListener('click', function() {
      document.getElementById('modalForm').classList.remove('is-active');
  });

  // Cerrar el modal al hacer clic en el fondo
  document.querySelector('.modal-background').addEventListener('click', function() {
      document.getElementById('modalForm').classList.remove('is-active');
  });
</script>