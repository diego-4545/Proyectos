<!-- filepath: c:\xampp\htdocs\RADCS\VISTAS\abogados.php -->
<div class="container is-fluid" style="margin-left: 20px; margin-top: 20px;">
  <h2 class="title">Información de las especialidades</h2>

  <div class="is-flex is-align-items-center" style="gap: 10px; margin-top: 10px;">
    <!-- Botón para agregar abogado -->
    <button class="button is-success" id="openModal">Agregar especialidad</button>

    <!-- Barra de búsqueda -->
    <form action="index.php?vista=especialidades" method="GET" class="is-flex" style="flex-grow: 1;">
      <input type="hidden" name="vista" value="especialidades">
      <div class="field has-addons" style="width: 100%;">
        <div class="control" style="flex-grow: 1;">
          <input class="input" type="text" name="busqueda" placeholder="Buscar por nombre" value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>">
        </div>
        <div class="control">
          <button class="button is-info" type="submit">Buscar</button>
        </div>
      </div>
    </form>

    <!-- Botón de regresar -->
    <a href="http://localhost/RADCS/index.php?vista=especialidades" class="button is-warning">Regresar</a>
  </div>

  <div class="form-rest mb-6 mt-6"></div>
</div>

<!-- Modal para agregar especialidad -->
<div class="modal" id="modalForm">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Agregar Especialidad</p>
      <button class="delete" aria-label="close" id="closeModal"></button>
    </header>
    <section class="modal-card-body">
      <form action="PHP/especialidad_guardar.php" method="POST" class="FormularioAjax" autocomplete="off">
        <div class="field">
          <label class="label">Nombre</label>
          <div class="control">
            <input class="input" type="text" name="nombre" placeholder="Nombre(s) de la especialidad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}" maxlength="100" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Descripción</label>
          <div class="control">
            <textarea class="textarea" name="descripcion" placeholder="Descripción de especialidad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,500}" maxlength="500" required></textarea>
          </div>
        </div>
        <footer class="modal-card-foot">
          <button type="submit" class="button is-success">Guardar</button>
        </footer>
      </form>
    </section>
  </div>
</div>

<!-- Modal para actualizar especialidad -->
<div class="modal" id="updateModal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Actualizar Especialidad</p>
      <button class="delete" aria-label="close" id="closeUpdateModal"></button>
    </header>
    <section class="modal-card-body">
      <form action="PHP/especialidad_actualizar.php" method="POST" class="FormularioAjax" autocomplete="off">
        <input type="hidden" name="id" id="update-id">
        <div class="field">
          <label class="label">Nombre</label>
          <div class="control">
            <input class="input" type="text" name="nombre" id="update-nombre" placeholder="Nombre(s) de la especialidad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}" maxlength="100" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Descripción</label>
          <div class="control">
            <textarea class="textarea" name="descripcion" id="update-descripcion" placeholder="Descripción de especialidad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,500}" maxlength="500" required></textarea>
          </div>
        </div>
        <footer class="modal-card-foot">
          <button type="submit" class="button is-success">Actualizar</button>
        </footer>
      </form>
    </section>
  </div>
</div>

<div class="container pb-6 pt-6">  
    <?php
        require_once "PHP/main.php";

        # Eliminar usuario #
        if (isset($_GET['user_id_del'])) {
            require_once "PHP/especialidad_eliminar.php";
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
        $url = "index.php?vista=especialidades&page=";
        $registros = 10;
        $busqueda = isset($_GET['busqueda']) ? limpiar_cadena($_GET['busqueda']) : "";

        # Paginador usuario #
        require_once "PHP/especialidad_lista.php";
    ?>
</div>

<script>
  // Modal para agregar especialidad
  const openModal = document.getElementById('openModal');
  const closeModal = document.getElementById('closeModal');
  const modal = document.getElementById('modalForm');

  openModal.addEventListener('click', () => {
    modal.classList.add('is-active');
  });

  closeModal.addEventListener('click', () => {
    modal.classList.remove('is-active');
  });

  // Modal para actualizar especialidad
  const updateModal = document.getElementById('updateModal');
  const closeUpdateModal = document.getElementById('closeUpdateModal');
  const openUpdateButtons = document.querySelectorAll('.open-update-modal');

  openUpdateButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Llenar los campos del formulario con los datos de especialidad
      document.getElementById('update-id').value = button.dataset.id;
      document.getElementById('update-nombre').value = button.dataset.nombre;
      document.getElementById('update-descripcion').value = button.dataset.descripcion;

      // Mostrar el modal
      updateModal.classList.add('is-active');
    });
  });

  closeUpdateModal.addEventListener('click', () => {
    updateModal.classList.remove('is-active');
  });
</script>