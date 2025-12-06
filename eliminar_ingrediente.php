<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    
    include_once 'conn.php';

    // Obtiene los IDs del producto y la materia prima a eliminar
    $id_producto = intval($_POST['id_producto']);
    $id_materia = intval($_POST['id_materia']);

    // Elimina el ingrediente específico de la formulación
    $sql = "DELETE FROM formulación WHERE ID_Producto = $id_producto AND ID_MateriaPrima = $id_materia";

    // Ejecuta la consulta y guarda el mensaje correspondiente en la sesión
    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = "Ingrediente eliminado correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar ingrediente.";
    }

    // Redirige de vuelta a la página de modificación de formulación del producto
    header("Location: modificar_formulacion.php?id=$id_producto");
    exit();
?>