<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

if (isset($busqueda) && $busqueda != "") {
    // Consulta para buscar por nombre, apellidos o cédula
    $consulta_datos = "SELECT * FROM abogados 
                       WHERE ((Id_abgd != '" . $_SESSION['id'] . "') 
                       AND (Nom_abgd LIKE '%$busqueda%' 
                       OR App_abgd LIKE '%$busqueda%' 
                       OR Apm_abgd LIKE '%$busqueda%' 
                       OR Ced_abgd LIKE '%$busqueda%')) 
                       ORDER BY Id_abgd ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(Id_abgd) FROM abogados 
                       WHERE ((Id_abgd != '" . $_SESSION['id'] . "') 
                       AND (Nom_abgd LIKE '%$busqueda%' 
                       OR App_abgd LIKE '%$busqueda%' 
                       OR Apm_abgd LIKE '%$busqueda%' 
                       OR Ced_abgd LIKE '%$busqueda%'))";
} else {
    // Consulta sin búsqueda
    $consulta_datos = "SELECT * FROM abogados 
                       WHERE Id_abgd != '" . $_SESSION['id'] . "' 
                       ORDER BY Id_abgd ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(Id_abgd) FROM abogados 
                       WHERE Id_abgd != '" . $_SESSION['id'] . "'";
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
                <th>Nombres</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Dirección</th>
                <th>Celular</th>
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
                <td>' . $rows['Ced_abgd'] . '</td>
                <td>' . $rows['Nom_abgd'] . '</td>
                <td>' . $rows['App_abgd'] . '</td>
                <td>' . $rows['Apm_abgd'] . '</td>
                <td>' . $rows['Dir_abgd'] . '</td>
                <td>' . $rows['Cel_abgd'] . '</td>
                <td>' . $rows['Tel_abgd'] . '</td>
                <td>' . $rows['Cor_abgd'] . '</td>
                <td>' . $rows['Con_abgd'] . '</td>
                <td>
                    <button class="button is-success is-rounded is-small open-update-modal" 
                        data-id="' . $rows['Id_abgd'] . '" 
                        data-cedula="' . $rows['Ced_abgd'] . '" 
                        data-nombre="' . $rows['Nom_abgd'] . '" 
                        data-apellido-paterno="' . $rows['App_abgd'] . '" 
                        data-apellido-materno="' . $rows['Apm_abgd'] . '" 
                        data-direccion="' . $rows['Dir_abgd'] . '" 
                        data-celular="' . $rows['Cel_abgd'] . '" 
                        data-telefono="' . $rows['Tel_abgd'] . '" 
                        data-correo="' . $rows['Cor_abgd'] . '">
                        Actualizar
                    </button>
                </td>
                <td>
                    <a href="index.php?vista=abogados&user_id_del=' . $rows['Id_abgd'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
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