<?php
session_start();
if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos']) || !isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}
include_once 'conn.php';

// Obtener solo productos que tienen salidas registradas
$productos = mysqli_query($conn, "
    SELECT p.ID, p.Nombre 
    FROM producto p
    INNER JOIN salidaproducto s ON p.ID = s.ID_Producto
    GROUP BY p.ID
    ORDER BY p.Nombre
");

$reportes = [];
while ($prod = mysqli_fetch_assoc($productos)) {
    $id_producto = $prod['ID'];
    $nombre_producto = $prod['Nombre'];

    // 1. Calcular costo promedio de cada materia prima usada en el producto
    $sql_formulacion = "SELECT f.ID_MateriaPrima, f.Cantidad, m.Nombre
                        FROM formulación f
                        INNER JOIN materiaprima m ON f.ID_MateriaPrima = m.ID
                        WHERE f.ID_Producto = $id_producto";
    $res_formulacion = mysqli_query($conn, $sql_formulacion);

    $costo_total = 0;
    $detalle_mp = [];
    while ($row = mysqli_fetch_assoc($res_formulacion)) {
        $id_mp = $row['ID_MateriaPrima'];
        $cantidad_mp = $row['Cantidad']; // cantidad usada para 10kg de producto
        $nombre_mp = $row['Nombre'];

        // Costo promedio de la materia prima (de entradas)
        $sql_prom = "SELECT AVG(Costo) as Promedio FROM entradamateria WHERE ID_Materia = $id_mp";
        $res_prom = mysqli_query($conn, $sql_prom);
        $row_prom = mysqli_fetch_assoc($res_prom);
        $costo_promedio = $row_prom && $row_prom['Promedio'] !== null ? floatval($row_prom['Promedio']) : 0;

        // Costo para la cantidad usada en 1kg de producto
        $costo_por_kg = ($cantidad_mp / 10) * $costo_promedio;
        $costo_total += $costo_por_kg;

        $detalle_mp[] = [
            'nombre' => $nombre_mp,
            'cantidad_kg' => $cantidad_mp / 10,
            'costo_promedio' => $costo_promedio,
            'costo_por_kg' => $costo_por_kg
        ];
    }

    // 2. Precio promedio de venta por kilo del producto (de salidaproducto)
    $sql_precio = "SELECT AVG(Precio) as PrecioPromedio FROM salidaproducto WHERE ID_Producto = $id_producto";
    $res_precio = mysqli_query($conn, $sql_precio);
    $row_precio = mysqli_fetch_assoc($res_precio);
    $precio_promedio = $row_precio && $row_precio['PrecioPromedio'] !== null ? floatval($row_precio['PrecioPromedio']) : 0;

    // 3. Ganancia estimada por kilo
    $ganancia = $precio_promedio - $costo_total;

    $reportes[] = [
        'nombre' => $nombre_producto,
        'detalle_mp' => $detalle_mp,
        'costo_total' => $costo_total,
        'precio_promedio' => $precio_promedio,
        'ganancia' => $ganancia
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Costos y Ganancias</title>
    <link rel="icon" type="image/x-icon" href="icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <nav>
            <a href="welcome.php">Inicio</a>
            <a href="productos.php">Inventario</a>
            <a href="produccion.php">Producción</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="reportes.php" class="active">Reportes</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <h2>Reporte de costos y ganancias por producto (por kilo)</h2>
        <?php foreach ($reportes as $rep): ?>
        <section style="margin-bottom:2rem;">
            <h3><?php echo htmlspecialchars($rep['nombre']); ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Materia Prima</th>
                        <th>Cantidad usada (kg)</th>
                        <th>Costo promedio por kg</th>
                        <th>Costo en 1kg de producto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rep['detalle_mp'] as $mp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mp['nombre']); ?></td>
                        <td><?php echo number_format($mp['cantidad_kg'], 3); ?></td>
                        <td>$<?php echo number_format($mp['costo_promedio'], 2); ?></td>
                        <td>$<?php echo number_format($mp['costo_por_kg'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Costo total de producción por kilo:</strong> $<?php echo number_format($rep['costo_total'], 2); ?></p>
            <p><strong>Precio promedio de venta por kilo:</strong> $<?php echo number_format($rep['precio_promedio'], 2); ?></p>
            <p><strong>Ganancia estimada por kilo:</strong> $<?php echo number_format($rep['ganancia'], 2); ?></p>
        </section>
        <?php endforeach; ?>
    </main>
</body>
</html>