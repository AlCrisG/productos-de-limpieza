<?php
    session_start();
    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    // Obtiene los datos enviados por POST
    $id_producto = intval($_POST['id_producto']);
    $id_materia = intval($_POST['id_materia']);
    $cantidad = floatval($_POST['cantidad']);

    // Evita duplicados: si el ingrediente ya existe en la formulación, actualiza la cantidad
    $sql_check = "SELECT * FROM formulación WHERE ID_Producto = $id_producto AND ID_MateriaPrima = $id_materia";
    $res_check = mysqli_query($conn, $sql_check);

    if ($res_check && mysqli_num_rows($res_check) > 0) {
        // Si ya existe, actualiza la cantidad
        $sql = "UPDATE formulación SET Cantidad = $cantidad WHERE ID_Producto = $id_producto AND ID_MateriaPrima = $id_materia";
    } else {
        // Si no existe, inserta un nuevo ingrediente
        $sql = "INSERT INTO formulación (ID_Producto, ID_MateriaPrima, Cantidad) VALUES ($id_producto, $id_materia, $cantidad)";
    }

    // Ejecuta la consulta y muestra un mensaje según el resultado
    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = "Ingrediente agregado/modificado correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al agregar/modificar ingrediente.";
    }

    // Redirige de vuelta a la página de modificación de formulación del producto
    header("Location: modificar_formulacion.php?id=$id_producto");
    exit();
?>