<?php
    session_start();
    include_once 'conn.php';

    if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'admin') {
        $_SESSION['mensaje'] = "No tienes permisos para realizar esta acción.";
        header("Location: usuarios.php");
        exit();
    }

    if (
        isset($_POST['Nombre'], $_POST['ApellidoPat'], $_POST['ApellidoMat'], $_POST['FechaIngreso'], $_POST['FechaNacimiento'],
            $_POST['LugarNacimiento'], $_POST['Calle'], $_POST['Colonia'], $_POST['Ciudad'], $_POST['Telefono'], $_POST['Rol'])
        && !empty($_POST['Nombre']) && !empty($_POST['ApellidoPat']) && !empty($_POST['ApellidoMat'])
        && !empty($_POST['FechaIngreso']) && !empty($_POST['FechaNacimiento']) && !empty($_POST['LugarNacimiento'])
        && !empty($_POST['Calle']) && !empty($_POST['Colonia']) && !empty($_POST['Ciudad'])
        && !empty($_POST['Telefono']) && !empty($_POST['Rol'])
    ) {
        $nombre = mysqli_real_escape_string($conn, $_POST['Nombre']);
        $apPat = mysqli_real_escape_string($conn, $_POST['ApellidoPat']);
        $apMat = mysqli_real_escape_string($conn, $_POST['ApellidoMat']);
        $fechaIngreso = mysqli_real_escape_string($conn, $_POST['FechaIngreso']);
        $fechaNacimiento = mysqli_real_escape_string($conn, $_POST['FechaNacimiento']);
        $lugarNacimiento = mysqli_real_escape_string($conn, $_POST['LugarNacimiento']);
        $calle = mysqli_real_escape_string($conn, $_POST['Calle']);
        $colonia = mysqli_real_escape_string($conn, $_POST['Colonia']);
        $ciudad = mysqli_real_escape_string($conn, $_POST['Ciudad']);
        $telefono = mysqli_real_escape_string($conn, $_POST['Telefono']);
        $rol = mysqli_real_escape_string($conn, $_POST['Rol']);

        $usuario = "NULL";
        $contrasena = "NULL";

        if ($rol === 'admin' || $rol === 'administrativo') {
            if (empty($_POST['Usuario']) || empty($_POST['Contrasena'])) {
                $_SESSION['mensaje'] = "Debes ingresar usuario y contraseña para este rol.";
                header("Location: usuarios.php");
                exit();
            }
            $usuario_val = mysqli_real_escape_string($conn, $_POST['Usuario']);
            $contrasena_val = mysqli_real_escape_string($conn, $_POST['Contrasena']);

            $check = mysqli_query($conn, "SELECT ID FROM empleado WHERE Usuario = '$usuario_val'");
            if ($check && mysqli_num_rows($check) > 0) {
                $_SESSION['mensaje'] = "El usuario ya existe, elige otro.";
                header("Location: usuarios.php");
                exit();
            }
            $usuario = "'$usuario_val'";
            $contrasena = "'$contrasena_val'";
        }

        $sql = "INSERT INTO empleado 
            (Nombre, ApellidoPat, ApellidoMat, FechaIngreso, FechaNacimiento, LugarNacimiento, Calle, Colonia, Ciudad, Teléfono, Rol, Usuario, Contraseña)
            VALUES (
                '$nombre', '$apPat', '$apMat', '$fechaIngreso', '$fechaNacimiento', '$lugarNacimiento',
                '$calle', '$colonia', '$ciudad', '$telefono', '$rol',
                $usuario, $contrasena
            )";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['mensaje'] = "Empleado registrado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al registrar el empleado.";
        }
    } else {
        $_SESSION['mensaje'] = "Datos incompletos o incorrectos.";
    }

    header("Location: usuarios.php");
    exit();
?>