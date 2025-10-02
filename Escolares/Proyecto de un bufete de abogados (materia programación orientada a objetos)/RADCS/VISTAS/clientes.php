<div class="container is-fluid" style="margin-left: 20px; margin-top: 20px;">
  <h2 class="title">Información de los clientes</h2>

  <div class="is-flex is-align-items-center" style="gap: 10px; margin-top: 10px;">
    <!-- Botón para agregar cliente -->
    <button class="button is-success" id="openModal">Agregar Cliente</button>

    <!-- Barra de búsqueda -->
    <form action="index.php?vista=clientes" method="GET" class="is-flex" style="flex-grow: 1;">
      <input type="hidden" name="vista" value="clientes">
      <div class="field has-addons" style="width: 100%;">
        <div class="control" style="flex-grow: 1;">
          <input class="input" type="text" name="busqueda" placeholder="Buscar por nombre o apellidos" value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>">
        </div>
        <div class="control">
          <button class="button is-info" type="submit">Buscar</button>
        </div>
      </div>
    </form>

    <!-- Botón de regresar -->
    <a href="http://localhost/RADCS/index.php?vista=clientes" class="button is-warning">Regresar</a>
  </div>

  <div class="form-rest mb-6 mt-6"></div>
</div>

<!-- Modal para agregar cliente -->
<div class="modal" id="modalForm">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Agregar Cliente</p>
      <button class="delete" aria-label="close" id="closeModal"></button>
    </header>
    <section class="modal-card-body">
      <form action="PHP/cliente_guardar.php" method="POST" class="FormularioAjax" autocomplete="off">
        <div class="field">
          <label class="label">Nombre</label>
          <div class="control">
            <input class="input" type="text" name="nombre" placeholder="Nombre(s) del cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Apellido Paterno</label>
          <div class="control">
            <input class="input" type="text" name="apellido_paterno" placeholder="Apellido paterno" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}" maxlength="15" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Apellido Materno</label>
          <div class="control">
            <input class="input" type="text" name="apellido_materno" placeholder="Apellido materno" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}" maxlength="15" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Dirección</label>
          <div class="control">
            <input class="input" type="text" name="direccion" placeholder="Dirección" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ 0-9]{3,80}" maxlength="80" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Código Postal</label>
          <div class="control">
            <input class="input" type="text" name="codigo_postal" placeholder="Código Postal" pattern="[0-9]{5}" maxlength="5" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Régimen Fiscal</label>
          <div class="control">
            <input class="input" type="text" name="regimen_fiscal" placeholder="Régimen Fiscal" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Teléfono</label>
          <div class="control">
            <input class="input" type="tel" name="telefono" placeholder="Número de teléfono" pattern="[0-9]{10}" maxlength="10" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Correo Electrónico</label>
          <div class="control">
            <input class="input" type="email" name="correo" placeholder="Correo electrónico" maxlength="80" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Contraseña</label>
          <div class="control">
            <input class="input" type="password" name="contrasena" placeholder="Contraseña (mínimo 7 caracteres)" pattern="[a-zA-Z0-9$@.-]{7,30}" maxlength="30" required>
          </div>
        </div>
        <footer class="modal-card-foot">
          <button type="submit" class="button is-success">Guardar</button>
        </footer>
      </form>
    </section>
  </div>
</div>

<!-- Modal para actualizar cliente -->
<div class="modal" id="updateModal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Actualizar Cliente</p>
      <button class="delete" aria-label="close" id="closeUpdateModal"></button>
    </header>
    <section class="modal-card-body">
      <form action="PHP/cliente_actualizar.php" method="POST" class="FormularioAjax" autocomplete="off">
        <input type="hidden" name="id" id="update-id">
        <div class="field">
          <label class="label">Nombre</label>
          <div class="control">
            <input class="input" type="text" name="nombre" id="update-nombre" placeholder="Nombre(s) del cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Apellido Paterno</label>
          <div class="control">
            <input class="input" type="text" name="apellido_paterno" id="update-apellido-paterno" placeholder="Apellido paterno" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}" maxlength="15" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Apellido Materno</label>
          <div class="control">
            <input class="input" type="text" name="apellido_materno" id="update-apellido-materno" placeholder="Apellido materno" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,15}" maxlength="15" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Dirección</label>
          <div class="control">
            <input class="input" type="text" name="direccion" id="update-direccion" placeholder="Dirección" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ 0-9]{3,80}" maxlength="80" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Código Postal</label>
          <div class="control">
            <input class="input" type="text" name="codigo_postal" id="update-codigo-postal" placeholder="Código Postal" pattern="[0-9]{5}" maxlength="5" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Régimen Fiscal</label>
          <div class="control">
            <input class="input" type="text" name="regimen_fiscal" id="update-regimen-fiscal" placeholder="Régimen Fiscal" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Teléfono</label>
          <div class="control">
            <input class="input" type="tel" name="telefono" id="update-telefono" placeholder="Número de teléfono" pattern="[0-9]{10}" maxlength="10" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Correo Electrónico</label>
          <div class="control">
            <input class="input" type="email" name="correo" id="update-correo" placeholder="Correo electrónico" maxlength="80" required>
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
            require_once "PHP/cliente_eliminar.php";
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
        $url = "index.php?vista=clientes&page=";
        $registros = 10;
        $busqueda = isset($_GET['busqueda']) ? limpiar_cadena($_GET['busqueda']) : "";

        # Paginador usuario #
        require_once "PHP/cliente_lista.php";
    ?>
</div>

<script>
  // Modal para agregar cliente
  const openModal = document.getElementById('openModal');
  const closeModal = document.getElementById('closeModal');
  const modal = document.getElementById('modalForm');

  openModal.addEventListener('click', () => {
    modal.classList.add('is-active');
  });

  closeModal.addEventListener('click', () => {
    modal.classList.remove('is-active');
  });

  // Modal para actualizar cliente
  const updateModal = document.getElementById('updateModal');
  const closeUpdateModal = document.getElementById('closeUpdateModal');
  const openUpdateButtons = document.querySelectorAll('.open-update-modal');

  openUpdateButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Llenar los campos del formulario con los datos del cliente
      document.getElementById('update-id').value = button.dataset.id;
      document.getElementById('update-nombre').value = button.dataset.nombre;
      document.getElementById('update-apellido-paterno').value = button.dataset.apellidoPaterno;
      document.getElementById('update-apellido-materno').value = button.dataset.apellidoMaterno;
      document.getElementById('update-direccion').value = button.dataset.direccion;
      document.getElementById('update-codigo-postal').value = button.dataset.codigoPostal;
      document.getElementById('update-regimen-fiscal').value = button.dataset.regimenFiscal;
      document.getElementById('update-telefono').value = button.dataset.telefono;
      document.getElementById('update-correo').value = button.dataset.correo;

      // Mostrar el modal
      updateModal.classList.add('is-active');
    });
  });

  closeUpdateModal.addEventListener('click', () => {
    updateModal.classList.remove('is-active');
  });
</script>