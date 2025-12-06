<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    $where = [];
    $orden = "em.Fecha DESC";
    $materia = isset($_GET['materia']) ? trim($_GET['materia']) : "";
    $ordenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : "fecha_desc";

    if ($materia !== "") {
        $materia = mysqli_real_escape_string($conn, $materia);
        $where[] = "(m.ID = '$materia' OR m.Nombre LIKE '%$materia%')";
    }
    if ($ordenar === "fecha_asc") {
        $orden = "em.Fecha ASC";
    } elseif ($ordenar === "costo_asc") {
        $orden = "em.Costo ASC";
    } elseif ($ordenar === "costo_desc") {
        $orden = "em.Costo DESC";
    }

    $sql = "SELECT em.Folio AS Folio, m.ID AS ID_Materia, m.Nombre, em.Fecha, em.Cantidad, em.Costo
            FROM entradamateria em
            INNER JOIN materiaprima m ON em.ID_Materia = m.ID";
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY $orden";
    $res = mysqli_query($conn, $sql);

    $materias = mysqli_query($conn, "SELECT ID, Nombre FROM materiaprima ORDER BY Nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas de Materias Primas</title>
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
            <a href="movimientos.php" class="active">Materias Primas</a>
            <a href="movimientos_productos.php">Productos</a>
        </nav>
    </header2>
    <main>
        <?php
            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: lightblue; font-size:30px; padding-bottom: 30px'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }
        ?>
        <h2>Entradas de Materia Prima</h2>
        <form method="get" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <input type="text" style="width: 400px;" name="materia" placeholder="Buscar por ID o nombre de materia" value="<?php echo htmlspecialchars($materia); ?>">
            <select name="ordenar" style="width: auto;">
                <option value="fecha_desc" <?php if($ordenar=="fecha_desc") echo "selected"; ?>>Fecha (más reciente)</option>
                <option value="fecha_asc" <?php if($ordenar=="fecha_asc") echo "selected"; ?>>Fecha (más antigua)</option>
                <option value="costo_desc" <?php if($ordenar=="costo_desc") echo "selected"; ?>>Costo (mayor)</option>
                <option value="costo_asc" <?php if($ordenar=="costo_asc") echo "selected"; ?>>Costo (menor)</option>
            </select>
            <button type="submit" style="width: fit-content;">Buscar</button>
            <a href="movimientos.php" style="margin-left:1rem;">Limpiar</a>
        </form>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>ID Materia</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Cantidad (Kg)</th>
                    <th>Costo por kilo ($)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Folio']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ID_Materia']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Cantidad']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Costo']) . "</td>";
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
            <h3>Registrar nueva entrada de materia prima</h3>
            <form method="post" action="registrar_entrada.php" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
                <select name="id_materia" required>
                    <option value="">Selecciona materia prima</option>
                    <?php
                        mysqli_data_seek($materias, 0);
                        while ($mat = mysqli_fetch_assoc($materias)) {
                            echo '<option value="' . $mat['ID'] . '">' . htmlspecialchars($mat['Nombre']) . '</option>';
                        }
                    ?>
                </select>
                <input type="date" name="fecha" required>
                <input type="number" step="0.01" min="0.01" name="cantidad" placeholder="Cantidad (Kg)" required>
                <input type="number" step="0.01" min="0.01" name="costo" placeholder="Costo por kilo ($)" required>
                <button type="submit">Registrar</button>
            </form>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>