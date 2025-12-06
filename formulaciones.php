<?php
    session_start();
    // Verifica que el usuario esté autenticado
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
    <title>Formulaciones</title>
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
            <a href="historial_produccion.php">Historial</a>
            <a href="formulaciones.php" class="active">Formulaciones</a>
        </nav>
    </header2>
    <main>
        
        <?php
            // Muestra mensaje de sesión si existe
            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: lightblue; font-size:30px; padding-bottom: 30px'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }
        ?>

        <!-- Formulario de búsqueda de productos -->
        <form method="get" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <input type="text" name="id" placeholder="ID" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
            <input type="text" name="codigo" placeholder="Código" value="<?php echo isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : ''; ?>">
            <input type="text" name="nombre" placeholder="Nombre" value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
            <button type="submit">Buscar</button>
            <a href="formulaciones.php" style="margin-left:1rem; margin-bottom:1rem">Limpiar</a>
        </form>
        <?php
            include_once 'conn.php';

            // Filtros para la búsqueda
            $where = [];
            if (!empty($_GET['id'])) {
                $id = mysqli_real_escape_string($conn, $_GET['id']);
                $where[] = "p.ID = '$id'";
            }
            if (!empty($_GET['codigo'])) {
                $codigo = mysqli_real_escape_string($conn, $_GET['codigo']);
                $where[] = "p.Código LIKE '%$codigo%'";
            }
            if (!empty($_GET['nombre'])) {
                $nombre = mysqli_real_escape_string($conn, $_GET['nombre']);
                $where[] = "p.Nombre LIKE '%$nombre%'";
            }

            // Consulta productos with filters applied
            $sql = "SELECT p.ID, p.Código, p.Nombre 
                    FROM producto p";
            if ($where) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            $productos = mysqli_query($conn, $sql);

            // Si hay productos, muestra la tabla
            if ($productos && mysqli_num_rows($productos) > 0) {
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID Producto</th><th>Código Producto</th><th>Nombre del Producto</th><th>Formulación</th><th>Acciones</th>';
                echo '</tr></thead><tbody>';
                while ($prod = mysqli_fetch_assoc($productos)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($prod['ID']) . '</td>';
                    echo '<td>' . htmlspecialchars($prod['Código']) . '</td>';
                    echo '<td>' . htmlspecialchars($prod['Nombre']) . '</td>';

                    // Obtener ingredientes de la formulación para este producto
                    $sqlIng = "SELECT f.Cantidad, m.Nombre 
                               FROM formulación f 
                               INNER JOIN materiaprima m ON f.ID_MateriaPrima = m.ID 
                               WHERE f.ID_Producto = " . intval($prod['ID']);
                    $ings = mysqli_query($conn, $sqlIng);

                    echo '<td>';
                    if ($ings && mysqli_num_rows($ings) > 0) {
                        $primero = true;
                        while ($ing = mysqli_fetch_assoc($ings)) {
                            if (!$primero) echo '<br>';
                            echo htmlspecialchars($ing['Nombre']) . ': ' . htmlspecialchars($ing['Cantidad']) . ' Kg';
                            $primero = false;
                        }
                    } else {
                        echo 'Sin ingredientes registrados';
                    }
                    echo '</td>';

                    // Mostrar botones solo si el usuario es admin
                    if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin') {
                        echo '<td style="white-space:nowrap;">
                                <a href="modificar_formulacion.php?id=' . urlencode($prod['ID']) . '" class="contrast">Editar</a>
                                <a href="eliminar_formulacion.php?id=' . urlencode($prod['ID']) . '" class="secondary"
                                   onclick="return confirm(\'¿Seguro que deseas eliminar toda la formulación de este producto?\')"
                                   style="margin-left:10px;">Eliminar</a>
                              </td>';
                    } else {
                        echo '<td style="white-space:nowrap; color:gray;">Sin permisos</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>No hay formulaciones para mostrar.</p>';
            }

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