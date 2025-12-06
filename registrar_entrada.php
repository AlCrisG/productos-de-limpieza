<?php
session_start();
include_once 'conn.php';

if (
    isset($_POST['id_materia'], $_POST['fecha'], $_POST['cantidad'], $_POST['costo']) &&
    is_numeric($_POST['id_materia']) &&
    is_numeric($_POST['cantidad']) &&
    is_numeric($_POST['costo'])
) {
    $id_materia = intval($_POST['id_materia']);
    $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
    $cantidad = floatval($_POST['cantidad']);
    $costo = floatval($_POST['costo']);

    $sql = "INSERT INTO entradamateria 
            VALUES (null, $id_materia, '$fecha', $cantidad, $costo)";
    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE materiaprima SET Existencia = Existencia + $cantidad WHERE ID = $id_materia");
        $_SESSION['mensaje'] = "Entrada registrada correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al registrar la entrada.";
    }
} else {
    $_SESSION['mensaje'] = "Datos incompletos o incorrectos.";
}

header("Location: movimientos.php");
exit();
?>