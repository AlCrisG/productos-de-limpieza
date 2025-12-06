<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'admin') {
        $_SESSION['mensaje'] = "No tienes permisos para realizar esta acción.";
        header("Location: usuarios.php");
        exit();
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $_SESSION['mensaje'] = "ID de usuario no válido.";
        header("Location: usuarios.php");
        exit();
    }

    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM empleado WHERE ID = $id");
    if (!$res || mysqli_num_rows($res) == 0) {
        $_SESSION['mensaje'] = "Usuario no encontrado.";
        header("Location: usuarios.php");
        exit();
    }
    $usuario = mysqli_fetch_assoc($res);

    // Procesar actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $campos = [
            'Nombre', 'ApellidoPat', 'ApellidoMat', 'FechaIngreso', 'FechaNacimiento',
            'LugarNacimiento', 'Calle', 'Colonia', 'Ciudad', 'Teléfono', 'Rol', 'Usuario'
        ];
        $updates = [];
        foreach ($campos as $campo) {
            if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
                $valor = mysqli_real_escape_string($conn, $_POST[$campo]);
                $updates[] = "$campo = '$valor'";
            }
        }
        // Contraseña solo si se proporciona
        if (!empty($_POST['Contrasena'])) {
            $hash = password_hash($_POST['Contrasena'], PASSWORD_DEFAULT);
            $updates[] = "Contrasena = '$hash'";
        }
        if ($updates) {
            $sql = "UPDATE empleado SET " . implode(', ', $updates) . " WHERE ID = $id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el usuario.";
            }
        } else {
            $_SESSION['mensaje'] = "No se realizaron cambios.";
        }
        header("Location: usuarios.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
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
            <a href="usuarios.php" class="active">Usuarios</a>
            <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                <a href="reportes.php">Reportes</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <h2>Modificar usuario (ID <?php echo htmlspecialchars($usuario['ID']); ?>)</h2>
        <form method="post" style="display:flex; flex-wrap:wrap; gap:1rem;">
            <input type="text" name="Nombre" placeholder="Nombre" value="">
            <input type="text" name="ApellidoPat" placeholder="Apellido Paterno" value="">
            <input type="text" name="ApellidoMat" placeholder="Apellido Materno" value="">
            <input type="date" name="FechaIngreso" placeholder="Fecha de Ingreso" value="">
            <input type="date" name="FechaNacimiento" placeholder="Fecha de Nacimiento" value="">
            <input type="text" name="LugarNacimiento" placeholder="Lugar de Nacimiento" value="">
            <input type="text" name="Calle" placeholder="Calle" value="">
            <input type="text" name="Colonia" placeholder="Colonia" value="">
            <input type="text" name="Ciudad" placeholder="Ciudad" value="">
            <input type="text" name="Teléfono" placeholder="Teléfono" value="">
            <select name="Rol">
                <option value="">Rol (actual: <?php echo htmlspecialchars($usuario['Rol']); ?>)</option>
                <option value="admin">Admin</option>
                <option value="administrativo">Administrativo</option>
                <option value="empleado">Empleado</option>
            </select>
            <input type="text" name="Usuario" placeholder="Usuario" value="">
            <input type="password" name="Contrasena" placeholder="Nueva contraseña (dejar vacío para no cambiar)">
            <button type="submit">Actualizar</button>
            <a href="usuarios.php" style="margin-left:1rem;">Cancelar</a>
        </form>
        <div style="margin-top:2rem;">
            <h4>Valores actuales:</h4>
            <ul>
                <li><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['Nombre']); ?></li>
                <li><strong>Apellido Paterno:</strong> <?php echo htmlspecialchars($usuario['ApellidoPat']); ?></li>
                <li><strong>Apellido Materno:</strong> <?php echo htmlspecialchars($usuario['ApellidoMat']); ?></li>
                <li><strong>Fecha Ingreso:</strong> <?php echo htmlspecialchars($usuario['FechaIngreso']); ?></li>
                <li><strong>Fecha Nacimiento:</strong> <?php echo htmlspecialchars($usuario['FechaNacimiento']); ?></li>
                <li><strong>Lugar Nacimiento:</strong> <?php echo htmlspecialchars($usuario['LugarNacimiento']); ?></li>
                <li><strong>Calle:</strong> <?php echo htmlspecialchars($usuario['Calle']); ?></li>
                <li><strong>Colonia:</strong> <?php echo htmlspecialchars($usuario['Colonia']); ?></li>
                <li><strong>Ciudad:</strong> <?php echo htmlspecialchars($usuario['Ciudad']); ?></li>
                <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['Teléfono']); ?></li>
                <li><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['Rol']); ?></li>
                <li><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['Usuario']); ?></li>
            </ul>
        </div>
    </main>
</body>
</html>