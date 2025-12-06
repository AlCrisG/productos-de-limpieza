<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    // Filtros de búsqueda
    $where = [];
    $order = "DESC";
    if (isset($_GET['orden']) && $_GET['orden'] === "antiguo") {
        $order = "ASC";
    }
    $id_lote = isset($_GET['id_lote']) ? trim($_GET['id_lote']) : "";
    $id_empleado = isset($_GET['id_empleado']) ? trim($_GET['id_empleado']) : "";
    $id_producto = isset($_GET['id_producto']) ? trim($_GET['id_producto']) : "";
    $fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : "";

    // Aplica los filtros si se proporcionan
    if ($id_lote !== "") {
        $id_lote = mysqli_real_escape_string($conn, $id_lote);
        $where[] = "l.ID = '$id_lote'";
    }
    if ($id_empleado !== "") {
        $id_empleado = mysqli_real_escape_string($conn, $id_empleado);
        $where[] = "e.ID = '$id_empleado'";
    }
    if ($id_producto !== "") {
        $id_producto = mysqli_real_escape_string($conn, $id_producto);
        $where[] = "p.ID = '$id_producto'";
    }
    if ($fecha !== "") {
        $fecha = mysqli_real_escape_string($conn, $fecha);
        $where[] = "l.Fecha >= '$fecha 00:00:00'";
    }

    // Consulta para obtener el historial de producción con los filtros aplicados
    $sql = "SELECT l.ID, l.Fecha, e.ID AS ID_Empleado, CONCAT(e.Nombre, ' ', e.ApellidoPat, ' ', e.ApellidoMat) AS NombreEmpleado, p.ID AS ID_Producto, p.Nombre AS NombreProducto, l.Cantidad
            FROM loteproducción l
            INNER JOIN empleado e ON l.ID_Empleado = e.ID
            INNER JOIN producto p ON l.ID_Producto = p.ID";
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY l.Fecha $order";
    $res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Producción</title>
    <link rel="icon" type="image/x-icon" href="icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <nav>
            <a href="welcome.php">Inicio</a>
            <a href="materia_prima.php">Inventario</a>
            <a href="produccion.php" class="active">Producción</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="usuarios.php">Usuarios</a>
            <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                <a href="reportes.php">Reportes</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <header2>
        <nav>
            <a href="produccion.php">Mandar a Producción</a>
            <a href="historial_produccion.php" class="active">Historial</a>
            <a href="formulaciones.php">Formulaciones</a>
        </nav>
    </header2>
    <main>
        <section>
            <h2>Historial de Producción</h2>
            <!-- Formulario de filtros de búsqueda -->
            <form method="get" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
                <input type="text" style="width: 200px;" name="id_lote" placeholder="ID Lote" value="<?php echo htmlspecialchars($id_lote); ?>">
                <input type="text" style="width: 200px;" name="id_empleado" placeholder="ID Empleado" value="<?php echo htmlspecialchars($id_empleado); ?>">
                <input type="text" style="width: 200px;" name="id_producto" placeholder="ID Producto" value="<?php echo htmlspecialchars($id_producto); ?>">
                <select name="orden" style="width: auto;">
                    <option value="reciente" <?php if($order=="DESC") echo "selected"; ?>>Más reciente primero</option>
                    <option value="antiguo" <?php if($order=="ASC") echo "selected"; ?>>Más antiguo primero</option>
                </select>
                <button type="submit">Buscar</button>
                <a href="historial_produccion.php" style="margin-left:1rem;">Limpiar</a>
            </form>
            <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID Lote</th>
                        <th>Fecha</th>
                        <th>ID Empleado</th>
                        <th>Nombre Empleado</th>
                        <th>ID Producto</th>
                        <th>Nombre Producto</th>
                        <th>Cantidad Producida (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Muestra los resultados de la consulta en la tabla
                if ($res && mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ID_Empleado']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['NombreEmpleado']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ID_Producto']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['NombreProducto']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Cantidad']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay registros para mostrar.</td></tr>";
                }
                ?>
                </tbody>
            </table>
            </div>
        </section>
    </main>
</body>
</html>