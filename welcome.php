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
    <title>Bienvenido</title>
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
            <a href="welcome.php" class="active">Inicio</a>
            <a href="materia_prima.php">Inventario</a>
            <a href="produccion.php">Producción</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="usuarios.php">Usuarios</a>
            <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                <a href="reportes.php">Reportes</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <h3>Sistema de Inventarios</h3>
        <h4>Bienvenido, <?php echo $_SESSION['Nombre'] . " " . $_SESSION['Apellidos']; ?></h4>
        <br><br>
        <img src="icon.png" alt="Imagen de Productos de Limpieza" style="width: 300px; height: auto;">
    </main>
</body>
</html>