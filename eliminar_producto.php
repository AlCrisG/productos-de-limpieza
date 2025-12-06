<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    
    include_once 'conn.php';

    // Obtiene el ID del producto a eliminar desde GET y lo escapa para seguridad
    $id = $_GET['id'];
    $id = mysqli_real_escape_string($conn, $id);

    // Prepara y ejecuta la consulta para eliminar el producto
    $sql = "DELETE FROM producto WHERE ID='$id'";
    $resultado = mysqli_query($conn, $sql);

    // Guarda el mensaje correspondiente en la sesión según el resultado
    if ($resultado) {
        $_SESSION['mensaje'] = "Producto eliminado correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el producto: " . mysqli_error($conn);
    }

    // Redirige de vuelta a la página de productos
    header("Location: productos.php");
    exit();
?>