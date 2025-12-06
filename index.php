<?php
    session_start();
    if (isset($_SESSION['Usuario'])) {
        header("Location: welcome.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="icon" type="image/x-icon" href="icon.png">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css"
    >
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            background-color:#2c3e50;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .container input {
            text-align: center;;
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Iniciar sesión</h1>
        <form action="login.php" method="POST">
            <div class="grid">
                <div>
                    <?php
                        if (isset($_SESSION['mensaje'])) {
                            echo "<p style='color: red;'>" . $_SESSION['mensaje'] . "</p>";
                            unset($_SESSION['mensaje']);
                        }
                    ?>
                <label for="username">Usuario</label>
                <input type="text" maxlength="20" id="usuario" name="usuario" required>
                <label for="password">Contraseña</label>
                <input type="password" maxlength="16" id="contrasena" name="contrasena" required>
            <button onclick="action()">Iniciar sesión</button>
        </form>
    </main>
</body>
</html>