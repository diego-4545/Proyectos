<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

if (isset($busqueda) && $busqueda != "") {
    // Consulta para buscar por nombre o descripción
    $consulta_datos = "SELECT * FROM especialidades 
                       WHERE (No_esp LIKE '%$busqueda%' 
                       OR Desc_esp LIKE '%$busqueda%') 
                       ORDER BY Id_esp ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(Id_esp) FROM especialidades 
                       WHERE (No_esp LIKE '%$busqueda%' 
                       OR Desc_esp LIKE '%$busqueda%')";
} else {
    // Consulta sin búsqueda
    $consulta_datos = "SELECT * FROM especialidades 
                       ORDER BY Id_esp ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(Id_esp) FROM especialidades";
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
                <th>Nombre</th>
                <th>Descripción</th>
                <th colspan="2">Opciones</th>
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
                <td>' . $rows['No_esp'] . '</td>
                <td>' . $rows['Desc_esp'] . '</td>
                <td>
                    <button class="button is-success is-rounded is-small open-update-modal" 
                        data-id="' . $rows['Id_esp'] . '" 
                        data-nombre="' . $rows['No_esp'] . '" 
                        data-descripcion="' . $rows['Desc_esp'] . '">
                        Actualizar
                    </button>
                </td>
                <td>
                    <a href="index.php?vista=especialidades&user_id_del=' . $rows['Id_esp'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                </td>
            </tr>
        ';
        $contador++;
    }
} else {
    $tabla .= '
        <tr class="has-text-centered">
            <td colspan="4">No se encontraron registros</td>
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