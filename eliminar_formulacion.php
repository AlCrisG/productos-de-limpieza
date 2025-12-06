<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }

    include_once 'conn.php';

    // Verifica que se haya recibido el ID del producto por GET
    if (isset($_GET['id'])) {
        $id_producto = intval($_GET['id']);
        // Elimina todas las filas de formulación asociadas al producto
        $sql = "DELETE FROM formulación WHERE ID_Producto = $id_producto";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['mensaje'] = "Formulación eliminada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar la formulación.";
        }
        // Redirige a la página de formulaciones
        header("Location: formulaciones.php");
        exit();
    } else {
        // Si no se especificó el ID, muestra mensaje de error y redirige
        $_SESSION['mensaje'] = "ID de producto no especificado.";
        header("Location: formulaciones.php");
        exit();
    }
?>