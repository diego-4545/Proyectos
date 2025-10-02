<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

if (isset($busqueda) && $busqueda != "") {
    // Consulta para buscar por cédula, nombre del abogado o especialidad
    $consulta_datos = "SELECT detalle_ae.id_dae, abogados.Ced_abgd, abogados.Nom_abgd, abogados.App_abgd, abogados.Apm_abgd, especialidades.No_esp 
                       FROM detalle_ae
                       INNER JOIN abogados ON detalle_ae.id_abgd_dae = abogados.Id_abgd
                       INNER JOIN especialidades ON detalle_ae.id_esp_dae = especialidades.Id_esp
                       WHERE (abogados.Ced_abgd LIKE '%$busqueda%' 
                       OR abogados.Nom_abgd LIKE '%$busqueda%' 
                       OR abogados.App_abgd LIKE '%$busqueda%' 
                       OR especialidades.No_esp LIKE '%$busqueda%')
                       ORDER BY detalle_ae.id_dae ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(detalle_ae.id_dae) 
                       FROM detalle_ae
                       INNER JOIN abogados ON detalle_ae.id_abgd_dae = abogados.Id_abgd
                       INNER JOIN especialidades ON detalle_ae.id_esp_dae = especialidades.Id_esp
                       WHERE (abogados.Ced_abgd LIKE '%$busqueda%' 
                       OR abogados.Nom_abgd LIKE '%$busqueda%' 
                       OR abogados.App_abgd LIKE '%$busqueda%' 
                       OR especialidades.No_esp LIKE '%$busqueda%')";
} else {
    // Consulta sin búsqueda
    $consulta_datos = "SELECT detalle_ae.id_dae, abogados.Ced_abgd, abogados.Nom_abgd, abogados.App_abgd, abogados.Apm_abgd, especialidades.No_esp 
                       FROM detalle_ae
                       INNER JOIN abogados ON detalle_ae.id_abgd_dae = abogados.Id_abgd
                       INNER JOIN especialidades ON detalle_ae.id_esp_dae = especialidades.Id_esp
                       ORDER BY detalle_ae.id_dae ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(detalle_ae.id_dae) 
                       FROM detalle_ae";
}

$conexion = conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas = ceil($total / $registros);

$tabla .= '
<div class="table-container">
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
            <tr class="has-text-centered">
                <th>#</th>
                <th>Cédula</th>
                <th>Nombre Completo</th>
                <th>Especialidad</th>
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
                <td>' . $rows['Ced_abgd'] . '</td>
                <td>' . $rows['Nom_abgd'] . ' ' . $rows['App_abgd'] . ' ' . $rows['Apm_abgd'] . '</td>
                <td>' . $rows['No_esp'] . '</td>
                <td>
                    <a href="index.php?vista=detalleae&user_id_del=' . $rows['id_dae'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
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
    echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
?>