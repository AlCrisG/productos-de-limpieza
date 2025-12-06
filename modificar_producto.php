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
    <title>Modificar Producto</title>
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
            <a href="materia_prima.php" class="active">Inventario</a>
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
        $id = $_GET['id'];
    ?>

    <form action="modificar.php">
        <h3>Deje vacío para no modificar</h3>
        <input type="hidden" name="tabla" value="producto">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <label for="codigo">Código:</label>
        <input type="text" id="codigo" name="codigo">
        
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre">
        
        <label for="existencia">Existencia (Kg):</label>
        <input type="number" min="0" step="0.01" id="existencia" name="existencia">
        
        <button onclick="action()">Modificar</button>
    </form>
    </main>
</body>
</html>