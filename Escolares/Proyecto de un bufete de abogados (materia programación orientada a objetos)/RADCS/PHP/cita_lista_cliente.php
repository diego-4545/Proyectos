<?php
// filepath: c:\xampp\htdocs\RADCS\PHP\cita_lista_cliente.php

require_once "PHP/main.php"; // Asegúrate de que la ruta a "main.php" sea correcta

$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

// Obtener el ID del cliente desde la sesión
$id_cliente = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

if ($id_cliente == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se especificó un cliente válido. Por favor, inicie sesión.
         </div>');
}

if (isset($busqueda) && $busqueda != "") {
    // Consulta para buscar citas de un cliente específico con búsqueda
    $consulta_datos = "SELECT cita.Id_ct, abogados.Nom_abgd, abogados.App_abgd, abogados.Apm_abgd, 
                              cita.Dia_ct AS fecha_cita, cita.Hora_ct AS hora_cita 
                       FROM cita
                       INNER JOIN abogados ON cita.Id_abgd_ct = abogados.Id_abgd
                       WHERE cita.Id_cli_ct = :id_cliente
                       AND (abogados.Nom_abgd LIKE :busqueda 
                       OR abogados.App_abgd LIKE :busqueda 
                       OR cita.Dia_ct LIKE :busqueda)
                       ORDER BY cita.Id_ct ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(cita.Id_ct) 
                       FROM cita
                       INNER JOIN abogados ON cita.Id_abgd_ct = abogados.Id_abgd
                       WHERE cita.Id_cli_ct = :id_cliente
                       AND (abogados.Nom_abgd LIKE :busqueda 
                       OR abogados.App_abgd LIKE :busqueda 
                       OR cita.Dia_ct LIKE :busqueda)";
} else {
    // Consulta sin búsqueda para un cliente específico
    $consulta_datos = "SELECT cita.Id_ct, abogados.Nom_abgd, abogados.App_abgd, abogados.Apm_abgd, 
                              cita.Dia_ct AS fecha_cita, cita.Hora_ct AS hora_cita 
                       FROM cita
                       INNER JOIN abogados ON cita.Id_abgd_ct = abogados.Id_abgd
                       WHERE cita.Id_cli_ct = :id_cliente
                       ORDER BY cita.Id_ct ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(cita.Id_ct) 
                       FROM cita
                       WHERE cita.Id_cli_ct = :id_cliente";
}

$conexion = conexion();

// Preparar y ejecutar las consultas
$datos_stmt = $conexion->prepare($consulta_datos);
if (isset($busqueda) && $busqueda != "") {
    $datos_stmt->execute([
        ":id_cliente" => $id_cliente,
        ":busqueda" => "%$busqueda%"
    ]);
} else {
    $datos_stmt->execute([":id_cliente" => $id_cliente]);
}
$datos = $datos_stmt->fetchAll();

$total_stmt = $conexion->prepare($consulta_total);
if (isset($busqueda) && $busqueda != "") {
    $total_stmt->execute([
        ":id_cliente" => $id_cliente,
        ":busqueda" => "%$busqueda%"
    ]);
} else {
    $total_stmt->execute([":id_cliente" => $id_cliente]);
}
$total = (int) $total_stmt->fetchColumn();

$Npaginas = ceil($total / $registros);

$tabla .= '
<div class="table-container">
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
            <tr class="has-text-centered">
                <th>#</th>
                <th>Nombre del Abogado</th>
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
                <td>' . htmlspecialchars($rows['Nom_abgd'] . ' ' . $rows['App_abgd'] . ' ' . $rows['Apm_abgd']) . '</td>
                <td>' . htmlspecialchars($rows['fecha_cita']) . '</td>
                <td>' . htmlspecialchars($rows['hora_cita']) . '</td>
                <td>
                    <a href="index.php?vista=home_cliente&cita_id_del=' . $rows['Id_ct'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
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
    echo paginador_tablas($pagina, $Npaginas, $url . "&id_cliente=" . $id_cliente, 7);
}
?>