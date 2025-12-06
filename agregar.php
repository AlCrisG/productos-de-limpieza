<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }

    include_once 'conn.php';

    // Si el formulario fue enviado por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tabla = $_POST['tabla']; // Puede ser 'materiaprima' o 'producto'
        $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);
        $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        $existencia = mysqli_real_escape_string($conn, $_POST['existencia']);

        // Prepara la consulta para insertar el nuevo registro
        $sql = "INSERT INTO $tabla VALUES (null, '$codigo', '$nombre', '$existencia')";

        // Verifica si ya existe un registro con el mismo código
        if (mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $tabla WHERE Código = '$codigo'"))) {
            $_SESSION['mensaje'] = "El código ya existe.";
        }
        // Si no existe, intenta insertar el registro
        else if (mysqli_query($conn, $sql)) {
            $_SESSION['mensaje'] = "Registro agregado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al agregar el registro: " . mysqli_error($conn);
        }

        // Redirige según la tabla a la que se agregó el registro
        if ($tabla == 'materiaprima') {
            header("Location: materia_prima.php");
            exit();
        } else {
            header("Location: productos.php");
            exit();
        }
    }
?>