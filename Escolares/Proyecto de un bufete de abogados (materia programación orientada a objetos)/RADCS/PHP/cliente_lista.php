<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

if (isset($busqueda) && $busqueda != "") {
    // Consulta para buscar por nombre, apellidos o correo
    $consulta_datos = "SELECT * FROM clientes 
                       WHERE (Nom_cli LIKE '%$busqueda%' 
                       OR App_cli LIKE '%$busqueda%' 
                       OR Apm_cli LIKE '%$busqueda%' 
                       OR Cor_cli LIKE '%$busqueda%') 
                       ORDER BY Id_cli ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(Id_cli) FROM clientes 
                       WHERE (Nom_cli LIKE '%$busqueda%' 
                       OR App_cli LIKE '%$busqueda%' 
                       OR Apm_cli LIKE '%$busqueda%' 
                       OR Cor_cli LIKE '%$busqueda%')";
} else {
    // Consulta sin búsqueda
    $consulta_datos = "SELECT * FROM clientes 
                       ORDER BY Id_cli ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(Id_cli) FROM clientes";
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
                <th>Nombres</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Dirección</th>
                <th>Código Postal</th>
                <th>Régimen Fiscal</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Contraseña</th>
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
                <td>' . $rows['Nom_cli'] . '</td>
                <td>' . $rows['App_cli'] . '</td>
                <td>' . $rows['Apm_cli'] . '</td>
                <td>' . $rows['Dir_cli'] . '</td>
                <td>' . $rows['Cp_cli'] . '</td>
                <td>' . $rows['Rf_cli'] . '</td>
                <td>' . $rows['Tel_cli'] . '</td>
                <td>' . $rows['Cor_cli'] . '</td>
                <td>' . $rows['Con_cli'] . '</td>
                <td>
                    <button class="button is-success is-rounded is-small open-update-modal" 
                        data-id="' . $rows['Id_cli'] . '" 
                        data-nombre="' . $rows['Nom_cli'] . '" 
                        data-apellido-paterno="' . $rows['App_cli'] . '" 
                        data-apellido-materno="' . $rows['Apm_cli'] . '" 
                        data-direccion="' . $rows['Dir_cli'] . '" 
                        data-codigo-postal="' . $rows['Cp_cli'] . '" 
                        data-regimen-fiscal="' . $rows['Rf_cli'] . '" 
                        data-telefono="' . $rows['Tel_cli'] . '" 
                        data-correo="' . $rows['Cor_cli'] . '">
                        Actualizar
                    </button>
                </td>
                <td>
                    <a href="index.php?vista=clientes&user_id_del=' . $rows['Id_cli'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                </td>
            </tr>
        ';
        $contador++;
    }
} else {
    $tabla .= '
        <tr class="has-text-centered">
            <td colspan="12">No se encontraron registros</td>
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