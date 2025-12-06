<?php
    session_start();
    include_once 'conn.php';

    if (
        isset($_POST['id_producto'], $_POST['fecha'], $_POST['cantidad'], $_POST['precio']) &&
        is_numeric($_POST['id_producto']) &&
        is_numeric($_POST['cantidad']) &&
        is_numeric($_POST['precio'])
    ) {
        $id_producto = intval($_POST['id_producto']);
        $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
        $cantidad = floatval($_POST['cantidad']);
        $precio = floatval($_POST['precio']);

        $res = mysqli_query($conn, "SELECT Existencia FROM producto WHERE ID = $id_producto");
        $row = mysqli_fetch_assoc($res);
        if ($row && $row['Existencia'] >= $cantidad) {
            $sql = "INSERT INTO salidaproducto (ID_Producto, Fecha, Cantidad, Precio) 
                    VALUES ($id_producto, '$fecha', $cantidad, $precio)";
            if (mysqli_query($conn, $sql)) {
                mysqli_query($conn, "UPDATE producto SET Existencia = Existencia - $cantidad WHERE ID = $id_producto");
                $_SESSION['mensaje'] = "Salida registrada correctamente.";
            } else {
                $_SESSION['mensaje'] = "Error al registrar la salida.";
            }
        } else {
            $_SESSION['mensaje'] = "No hay suficiente producto en existencia para realizar la salida.";
        }
    } else {
        $_SESSION['mensaje'] = "Datos incompletos o incorrectos.";
    }

    header("Location: movimientos_productos.php");
    exit();
?>