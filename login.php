<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <?php
        include_once 'conn.php';

        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];
        $consultaUsuario = "SELECT * FROM empleado WHERE Usuario = '$usuario';";
        $resultado = mysqli_query($conn, $consultaUsuario);
        $fila = mysqli_fetch_assoc($resultado);
        if ($fila) {
            if ($fila['Contraseña'] == $contrasena) {
                $_SESSION['Usuario'] = $usuario;
                $_SESSION['Rol'] = $fila['Rol'];
                $_SESSION['Nombre'] = $fila['Nombre'];
                $_SESSION['Apellidos'] = $fila['ApellidoPat'] . " " . $fila['ApellidoMat'];
                $_SESSION['IdEmpleado'] = $fila['ID'];
                header("Location: welcome.php");
                exit();
            }
        }

        $_SESSION['mensaje'] = "Usuario o contraseña incorrectos.";
        header("Location: index.php");
    ?>
</body>
</html>