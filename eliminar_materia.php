<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    
    include_once 'conn.php';

    // Obtiene el ID de la materia prima a eliminar desde GET y lo escapa para seguridad
    $id = $_GET['id'];
    $id = mysqli_real_escape_string($conn, $id);

    // Prepara y ejecuta la consulta para eliminar la materia prima
    $sql = "DELETE FROM materiaprima WHERE ID='$id'";
    $resultado = mysqli_query($conn, $sql);

    // Guarda el mensaje correspondiente en la sesión según el resultado
    if ($resultado) {
        $_SESSION['mensaje'] = "Materia prima eliminada correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar la materia prima: " . mysqli_error($conn);
    }

    // Redirige de vuelta a la página de materia prima
    header("Location: materia_prima.php");
    exit();
?>