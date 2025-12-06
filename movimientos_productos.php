<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    $where = [];
    $orden = "sp.Fecha DESC";
    $producto = isset($_GET['producto']) ? trim($_GET['producto']) : "";
    $ordenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : "fecha_desc";

    if ($producto !== "") {
        $producto = mysqli_real_escape_string($conn, $producto);
        $where[] = "(p.ID = '$producto' OR p.Nombre LIKE '%$producto%')";
    }
    if ($ordenar === "fecha_asc") {
        $orden = "sp.Fecha ASC";
    } elseif ($ordenar === "precio_asc") {
        $orden = "sp.Precio ASC";
    } elseif ($ordenar === "precio_desc") {
        $orden = "sp.Precio DESC";
    }

    $sql = "SELECT sp.Folio AS Folio, p.ID AS ID_Producto, p.Nombre, sp.Fecha, sp.Cantidad, sp.Precio
            FROM salidaproducto sp
            INNER JOIN producto p ON sp.ID_Producto = p.ID";
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY $orden";
    $res = mysqli_query($conn, $sql);

    $productos = mysqli_query($conn, "SELECT ID, Nombre FROM producto ORDER BY Nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salidas de Productos</title>
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
            <a href="movimientos.php" class="active">Movimientos</a>
            <a href="usuarios.php">Usuarios</a>
            <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                <a href="reportes.php">Reportes</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <header2>
        <nav>
            <a href="movimientos.php">Materias Primas</a>
            <a href="movimientos_productos.php" class="active">Productos</a>
        </nav>
    </header2>
    <main>
        <?php
            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: lightblue; font-size:30px; padding-bottom: 30px'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }
        ?>
        
        <h2>Salidas de Productos</h2>
        <form method="get" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <input type="text" style="width: 400px;" name="producto" placeholder="Buscar por ID o nombre de producto" value="<?php echo htmlspecialchars($producto); ?>">
            <select name="ordenar" style="width: auto;">
                <option value="fecha_desc" <?php if($ordenar=="fecha_desc") echo "selected"; ?>>Fecha (más reciente)</option>
                <option value="fecha_asc" <?php if($ordenar=="fecha_asc") echo "selected"; ?>>Fecha (más antigua)</option>
                <option value="precio_desc" <?php if($ordenar=="precio_desc") echo "selected"; ?>>Precio (mayor)</option>
                <option value="precio_asc" <?php if($ordenar=="precio_asc") echo "selected"; ?>>Precio (menor)</option>
            </select>
            <button type="submit" style="width: fit-content;">Buscar</button>
            <a href="movimientos_productos.php" style="margin-left:1rem;">Limpiar</a>
        </form>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Cantidad (Kg)</th>
                    <th>Precio por kilo ($)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Folio']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ID_Producto']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Cantidad']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Precio']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay movimientos registrados.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        </div>

        <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
        <section style="margin-top:3rem;">
            <h3>Registrar nueva salida de producto</h3>
            <form method="post" action="registrar_salida.php" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
                <select name="id_producto" required>
                    <option value="">Selecciona producto</option>
                    <?php
                        mysqli_data_seek($productos, 0);
                        while ($prod = mysqli_fetch_assoc($productos)) {
                            echo '<option value="' . $prod['ID'] . '">' . htmlspecialchars($prod['Nombre']) . '</option>';
                        }
                    ?>
                </select>
                <input type="date" name="fecha" required>
                <input type="number" step="0.01" min="0.01" name="cantidad" placeholder="Cantidad (Kg)" required>
                <input type="number" step="0.01" min="0.01" name="precio" placeholder="Precio por kilo ($)" required>
                <button type="submit">Registrar</button>
            </form>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>