<?php
require_once "PHP/main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

// Obtener el ID del abogado desde la sesión
$id_abogado = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

if ($id_abogado == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se especificó un abogado válido. Por favor, inicie sesión.
         </div>');
}

if (isset($busqueda) && $busqueda != "") {
    // Consulta para buscar citas de un abogado específico con búsqueda
    $consulta_datos = "SELECT cita.Id_ct, clientes.Nom_cli, clientes.App_cli, clientes.Apm_cli, 
                              cita.Dia_ct AS fecha_cita, cita.Hora_ct AS hora_cita 
                       FROM cita
                       INNER JOIN clientes ON cita.Id_cli_ct = clientes.Id_cli
                       WHERE cita.Id_abgd_ct = :id_abogado
                       AND (clientes.Nom_cli LIKE '%$busqueda%' 
                       OR clientes.App_cli LIKE '%$busqueda%' 
                       OR cita.Dia_ct LIKE '%$busqueda%')
                       ORDER BY cita.Id_ct ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(cita.Id_ct) 
                       FROM cita
                       INNER JOIN clientes ON cita.Id_cli_ct = clientes.Id_cli
                       WHERE cita.Id_abgd_ct = :id_abogado
                       AND (clientes.Nom_cli LIKE '%$busqueda%' 
                       OR clientes.App_cli LIKE '%$busqueda%' 
                       OR cita.Dia_ct LIKE '%$busqueda%')";
} else {
    // Consulta sin búsqueda para un abogado específico
    $consulta_datos = "SELECT cita.Id_ct, clientes.Nom_cli, clientes.App_cli, clientes.Apm_cli, 
                              cita.Dia_ct AS fecha_cita, cita.Hora_ct AS hora_cita 
                       FROM cita
                       INNER JOIN clientes ON cita.Id_cli_ct = clientes.Id_cli
                       WHERE cita.Id_abgd_ct = :id_abogado
                       ORDER BY cita.Id_ct ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(cita.Id_ct) 
                       FROM cita
                       WHERE cita.Id_abgd_ct = :id_abogado";
}

$conexion = conexion();

// Preparar y ejecutar las consultas
$datos_stmt = $conexion->prepare($consulta_datos);
$datos_stmt->execute([":id_abogado" => $id_abogado]);
$datos = $datos_stmt->fetchAll();

$total_stmt = $conexion->prepare($consulta_total);
$total_stmt->execute([":id_abogado" => $id_abogado]);
$total = (int) $total_stmt->fetchColumn();

$Npaginas = ceil($total / $registros);

$tabla .= '
<div class="table-container">
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
            <tr class="has-text-centered">
                <th>#</th>
                <th>Nombre Cliente</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
';

if ($total >= 1 && $pagina <= $Npaginas) {
    $contador = $inicio + 1;
    foreach ($datos as $rows) {
        $tabla .= '
            <tr class="has-text-centered">
                <td>' . $contador . '</td>
                <td>' . $rows['Nom_cli'] . ' ' . $rows['App_cli'] . ' ' . $rows['Apm_cli'] . '</td>
                <td>' . $rows['fecha_cita'] . '</td>
                <td>' . $rows['hora_cita'] . '</td>
                <td>
                    <a href="index.php?vista=home&cita_id_del=' . $rows['Id_ct'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                </td>
            </tr>
        ';
        $contador++;
    }
} else {
    $tabla .= '
        <tr class="has-text-centered">
            <td colspan="5">No se encontraron registros</td>
        </tr>
    ';
}

$tabla .= '
        </tbody>
    </table>
</div>
';

echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
    echo paginador_tablas($pagina, $Npaginas, $url . "&id_abogado=" . $id_abogado, 7);
}
?>