<?php
require_once "PHP/main.php"; // Asegúrate de que la conexión esté configurada correctamente

// Conexión a la base de datos
$conexion = conexion();

// Obtener el ID del cliente desde la sesión
$id_cliente = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

if ($id_cliente == 0) {
    die('<div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            No se especificó un cliente válido. Por favor, inicie sesión.
         </div>');
}

// Consulta para obtener las facturas únicas del cliente ordenadas por ID de factura
$facturas_query = $conexion->prepare("
    SELECT MIN(Id_fc) AS Id_fc, fc_fc, tot_fc 
    FROM factura 
    WHERE Id_cli_fc = :id_cliente 
    GROUP BY fc_fc, tot_fc
    ORDER BY Id_fc ASC
");
$facturas_query->execute([':id_cliente' => $id_cliente]);
$facturas = $facturas_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container is-fluid" style="margin-top: 20px;">
    <h2 class="title has-text-centered">Sus facturas</h2>

    <?php if (count($facturas) > 0): ?>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth" style="border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; border: 2px solid #333;">
            <thead style="background-color: #f5f5f5; border-bottom: 2px solid #333;">
                <tr class="has-text-centered">
                    <th>ID Factura</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                    <tr class="has-text-centered" style="border-bottom: 1px solid #333;">
                        <td><?php echo htmlspecialchars($factura['Id_fc']); ?></td>
                        <td><?php echo htmlspecialchars($factura['fc_fc']); ?></td>
                        <td>$<?php echo number_format($factura['tot_fc'], 2); ?></td>
                        <td>
                            <a href="index.php?vista=ver_factura&id=<?php echo htmlspecialchars($factura['Id_fc']); ?>" class="button is-info is-small">
                                Ver factura
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="has-text-centered">No se encontraron facturas para usted.</p>
    <?php endif; ?>
</div>