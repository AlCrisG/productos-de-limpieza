<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto'], $_POST['cantidad'])) {
        $id_producto = intval($_POST['producto']);
        $cantidad_producir = floatval($_POST['cantidad']);
        $id_empleado = $_SESSION['IdEmpleado'];

        $sql = "SELECT f.ID_MateriaPrima, f.Cantidad, m.Existencia, m.Nombre
                FROM formulación f
                INNER JOIN materiaprima m ON f.ID_MateriaPrima = m.ID
                WHERE f.ID_Producto = $id_producto";
        $res = mysqli_query($conn, $sql);

        $suficiente = true;
        $faltantes = [];
        $materias = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $cantidad_necesaria = ($cantidad_producir / 10) * $row['Cantidad'];
            if ($row['Existencia'] < $cantidad_necesaria) {
                $suficiente = false;
                $faltantes[] = $row['Nombre'];
            }
            $materias[] = [
                'id' => $row['ID_MateriaPrima'],
                'cantidad_necesaria' => $cantidad_necesaria
            ];
        }

        if (!$suficiente) {
            $_SESSION['mensaje'] = "No hay suficiente materia prima para producir. Faltan: " . implode(', ', $faltantes);
        } else {
            foreach ($materias as $mat) {
                $id_mat = $mat['id'];
                $cant_mat = $mat['cantidad_necesaria'];
                mysqli_query($conn, "UPDATE materiaprima SET Existencia = Existencia - $cant_mat WHERE ID = $id_mat");
            }

            mysqli_query($conn, "UPDATE producto SET Existencia = Existencia + $cantidad_producir WHERE ID = $id_producto");

            $fecha = date('Y-m-d H:i:s');
            $sql_lote = "INSERT INTO loteproducción (Fecha, ID_Empleado, ID_Producto, Cantidad) 
                         VALUES ('$fecha', $id_empleado, $id_producto, $cantidad_producir)";
            mysqli_query($conn, $sql_lote);

            $_SESSION['mensaje'] = "Producción registrada y materias primas descontadas correctamente.";
        }
        header("Location: produccion.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción</title>
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
            <a href="produccion.php" class="active">Mandar a Producción</a>
            <a href="historial_produccion.php">Historial</a>
            <a href="formulaciones.php">Formulaciones</a>
        </nav>
    </header2>
    <main>
        <section>
            <h2>Producción de productos</h2>
            <form method="post" action="">
                <select style="height: 60px;" name="producto" id="producto" required>
                    <option value="" disabled selected>Producto a Producir</option>
                    <?php
                        include_once 'conn.php';
                        // Solo productos con formulación
                        $sql = "SELECT p.ID, p.Nombre 
                                FROM producto p
                                WHERE EXISTS (
                                    SELECT 1 FROM formulación f WHERE f.ID_Producto = p.ID
                                )";
                        $resultado = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($resultado) > 0) {
                            while ($row = mysqli_fetch_assoc($resultado)) {
                                echo "<option value='" . $row['ID'] . "'>" . htmlspecialchars($row['Nombre']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>No hay productos con formulación</option>";
                        }
                    ?>
                </select>
                <input type="number" name="cantidad" min="0.01" step="0.01" placeholder="Cantidad a producir (Kg)" required style="height: 60px;">
                <button type="submit" style="height: 60px;">Producir</button>
            </form>
            <?php
            // Mensaje de éxito o error
            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: blue; font-size: 1.2em;'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }
            ?>
        </section>
    </main>
</body>
</html>