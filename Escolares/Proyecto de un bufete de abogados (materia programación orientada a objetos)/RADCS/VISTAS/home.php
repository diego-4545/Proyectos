<div class="container is-fluid" style="margin-left: 20px; margin-top: 20px;">

<h2 class="title">Bienvenido de nuevo Lic. <?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario'; ?></h2>
<h2 class="subtitle">Aqui tiene sus citas</h2>

</div>

<?php
require_once "PHP/main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

$conexion = conexion();
if (!$conexion) {
    die("Error al conectar a la base de datos");
}

?>

<div class="container is-fluid" style="margin-top: 20px;">

  <!-- Contenedor para botones y barra de búsqueda -->
  <div class="is-flex is-align-items-center" style="gap: 10px; margin-top: 10px;">
    <!-- Botón para abrir el modal -->
    <a href="http://localhost/RADCS/index.php?vista=cita" class="button is-success">Agregar Cita</a>
    <!-- Barra de búsqueda -->
    <form action="index.php?vista=home" method="GET" class="is-flex" style="flex-grow: 1;">
      <input type="hidden" name="vista" value="home">
      <div class="field has-addons" style="width: 100%;">
        <div class="control" style="flex-grow: 1;">
          <input class="input" type="text" name="busqueda" placeholder="Buscar por nombre de cliente fecha u hora" value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>">
        </div>
        <div class="control">
          <button class="button is-info" type="submit">Buscar</button>
        </div>
      </div>
    </form>

    <!-- Botón de regresar -->
    <a href="http://localhost/RADCS/index.php?vista=home" class="button is-warning">Regresar</a>

    <!-- Botón para eliminar citas ya pasadas -->
    <button id="btn-eliminar-citas" class="button is-danger">Eliminar citas ya pasadas</button>
  </div>

  <div class="form-rest mb-6 mt-6"></div>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "PHP/main.php";

        # Eliminar usuario #
        if (isset($_GET['cita_id_del'])) {
            require_once "PHP/cita_eliminar.php";
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
        $url = "index.php?vista=home&page=";
        $registros = 10;
        $busqueda = isset($_GET['busqueda']) ? limpiar_cadena($_GET['busqueda']) : "";

        # Paginador usuario #
        require_once "PHP/cita_lista.php";
    ?>
</div>

<script>
    // Función para eliminar citas ya pasadas
    document.getElementById('btn-eliminar-citas').addEventListener('click', function () {
        if (confirm("¿Está seguro de que desea eliminar todas las citas pasadas?")) {
            fetch('PHP/eliminar_citas_pasadas.php', {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                alert("Citas pasadas eliminadas correctamente.");
                location.reload(); // Recargar la página para reflejar los cambios
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Ocurrió un error al intentar eliminar las citas pasadas.");
            });
        }
    });
</script>