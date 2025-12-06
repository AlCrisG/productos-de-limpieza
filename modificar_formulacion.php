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
    <title>Modificar Formulación</title>
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
            <a href="historial_produccion.php">Historial</a>
            <a href="formulaciones.php" class="active">Formulaciones</a>
        </nav>
    </header2>
    <main>
        <?php
            include_once 'conn.php';
            $id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: lightblue; font-size:30px; padding-bottom: 30px'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }

            // Mostrar ingredientes actuales
            echo "<h3>Ingredientes actuales</h3>";
            $sql = "SELECT f.ID_MateriaPrima, f.Cantidad, m.Nombre 
                    FROM formulación f 
                    INNER JOIN materiaprima m ON f.ID_MateriaPrima = m.ID 
                    WHERE f.ID_Producto = $id_producto";
            $res = mysqli_query($conn, $sql);
            if ($res && mysqli_num_rows($res) > 0) {
                echo '<table><thead><tr><th>Ingrediente</th><th>Cantidad (Kg)</th><th>Acción</th></tr></thead><tbody>';
                while ($row = mysqli_fetch_assoc($res)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['Nombre']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['Cantidad']) . '</td>';
                    echo '<td>
                        <form method="post" action="eliminar_ingrediente.php" style="display:inline;">
                            <input type="hidden" name="id_producto" value="' . $id_producto . '">
                            <input type="hidden" name="id_materia" value="' . $row['ID_MateriaPrima'] . '">
                            <button type="submit" onclick="return confirm(\'¿Eliminar este ingrediente?\')">Eliminar</button>
                        </form>
                    </td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo "<p>No hay ingredientes en esta formulación.</p>";
            }

            // Formulario para agregar ingrediente
            // Obtener materias primas disponibles
            $materias = mysqli_query($conn, "SELECT ID, Nombre FROM materiaprima ORDER BY Nombre");
        ?>
        <h3>Agregar ingrediente</h3>
        <form method="post" action="agregar_ingrediente.php" style="display:flex; gap:1rem; align-items:center;">
            <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
            <select name="id_materia" required>
                <option value="">Selecciona materia prima</option>
                <?php
                    while ($mat = mysqli_fetch_assoc($materias)) {
                        echo '<option value="' . $mat['ID'] . '">' . htmlspecialchars($mat['Nombre']) . '</option>';
                    }
                ?>
            </select>
            <input type="number" step="0.01" min="0.01" name="cantidad" placeholder="Cantidad (Kg)" required>
            <button type="submit">Agregar</button>
        </form>
    </main>
</body>
</html>