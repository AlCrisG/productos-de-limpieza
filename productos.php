<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Productos</title>
    <link rel="icon" type="image/x-icon" href="icon.png">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css"
    >
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <nav>
            <a href="welcome.php">Inicio</a>
            <a href="productos.php" class="active">Inventario</a>
            <a href="produccion.php">Producción</a>
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
            <a href="materia_prima.php">Materias Primas</a>
            <a href="productos.php" class="active">Productos</a>
        </nav>
    </header2>
    <main>

        <?php
            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: lightblue; font-size:30px; padding-bottom: 30px'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }
        ?>

        <form method="get" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <input type="text" name="id" placeholder="ID" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
            <input type="text" maxlength="8" name="codigo" placeholder="Código" value="<?php echo isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : ''; ?>">
            <input type="text" maxlength="45" name="nombre" placeholder="Nombre" value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
            <input type="number" min="0" step="0.01" name="existencia_min" placeholder="Existencia mín." value="<?php echo isset($_GET['existencia_min']) ? htmlspecialchars($_GET['existencia_min']) : ''; ?>">
            <input type="number" min="0" step="0.01" name="existencia_max" placeholder="Existencia máx." value="<?php echo isset($_GET['existencia_max']) ? htmlspecialchars($_GET['existencia_max']) : ''; ?>">
            <button type="submit">Buscar</button>
            <a href="productos.php" style="margin-left:1rem; margin-bottom:1rem">Limpiar</a>
        </form>
        <?php
            include_once 'conn.php';

            // Construir consulta con filtros
            $where = [];
            if (!empty($_GET['id'])) {
                $id = mysqli_real_escape_string($conn, $_GET['id']);
                $where[] = "ID = '$id'";
            }
            if (!empty($_GET['codigo'])) {
                $codigo = mysqli_real_escape_string($conn, $_GET['codigo']);
                $where[] = "Código LIKE '%$codigo%'";
            }
            if (!empty($_GET['nombre'])) {
                $nombre = mysqli_real_escape_string($conn, $_GET['nombre']);
                $where[] = "Nombre LIKE '%$nombre%'";
            }
            if (isset($_GET['existencia_min']) && $_GET['existencia_min'] !== "") {
                $min = floatval($_GET['existencia_min']);
                $where[] = "Existencia >= $min";
            }
            if (isset($_GET['existencia_max']) && $_GET['existencia_max'] !== "") {
                $max = floatval($_GET['existencia_max']);
                $where[] = "Existencia <= $max";
            }

            $sql = "SELECT * FROM producto";
            if ($where) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $resultado = mysqli_query($conn, $sql);

            if ($resultado && mysqli_num_rows($resultado) > 0) {
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Código</th><th>Nombre</th><th>Existencia (Kg)</th><th>Acciones</th>';
                echo '</tr></thead><tbody>';
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($fila['ID']) . '</td>';
                    echo '<td>' . htmlspecialchars($fila['Código']) . '</td>';
                    echo '<td>' . htmlspecialchars($fila['Nombre']) . '</td>';
                    echo '<td>' . htmlspecialchars($fila['Existencia']) . '</td>';
                    // Solo mostrar botones si es admin
                    if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin') {
                        echo '<td>
                                <a href="modificar_producto.php?id=' . urlencode($fila['ID']) . '" class="contrast">Editar</a>
                                <a href="eliminar_producto.php?id=' . urlencode($fila['ID']) . '" class="secondary" 
                                    onclick="return confirm(\'¿Seguro que deseas eliminar este producto?\')" style="padding-left:20px">Eliminar</a>
                              </td>';
                    } else {
                        echo '<td style="white-space:nowrap; color:gray;">Sin permisos</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>No hay productos para mostrar.</p>';
            }
        ?>
        <?php
        // Mostrar sección para agregar productos solo si el usuario es admin
        if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin') {
            echo '
            <section style="margin-top:3rem;">
                <h3>Agregar nuevo producto</h3>
                <form method="post" action="agregar.php" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="tabla" value="producto">
                    <input type="text" maxlength="8" name="codigo" placeholder="Código" required>
                    <input type="text" maxlength="45" name="nombre" placeholder="Nombre" required>
                    <input type="number" min="0" step="0.01" name="existencia" placeholder="Existencia (Kg)" required>
                    <button type="submit">Agregar</button>
                </form>
            </section>
            ';
        }
        ?>
    </main>
</body>
</html>