<?php
    session_start();
    include_once 'conn.php';

    // Verifica que el usuario sea admin
    if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'admin') {
        $_SESSION['mensaje'] = "No tienes permisos para realizar esta acción.";
        header("Location: usuarios.php");
        exit();
    }

    // Verifica que se haya recibido un ID válido por GET
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = intval($_GET['id']);

        // Evita que el admin se elimine a sí mismo
        if (isset($_SESSION['IdEmpleado']) && $_SESSION['IdEmpleado'] == $id) {
            $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario.";
            header("Location: usuarios.php");
            exit();
        }

        // Prepara y ejecuta la consulta para eliminar el usuario
        $sql = "DELETE FROM empleado WHERE ID = $id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el usuario.";
        }
    } else {
        $_SESSION['mensaje'] = "ID de usuario no válido.";
    }

    // Redirige de vuelta a la página de usuarios
    header("Location: usuarios.php");
    exit();
?>