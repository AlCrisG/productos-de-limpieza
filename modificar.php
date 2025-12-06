<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    
    include_once 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $tabla = $_GET['tabla'];
        $id = $_GET['id'];
        $id = mysqli_real_escape_string($conn, $id);
        $codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : "";
        $nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : "";
        $existencia = isset($_GET['existencia']) ? trim($_GET['existencia']) : "";

        // Construir solo los campos que no estén vacíos
        $campos = [];
        if ($codigo !== "") {
            $codigo = mysqli_real_escape_string($conn, $codigo);
            $campos[] = "Código='$codigo'";
        }
        if ($nombre !== "") {
            $nombre = mysqli_real_escape_string($conn, $nombre);
            $campos[] = "Nombre='$nombre'";
        }
        if ($existencia !== "") {
            $existencia = mysqli_real_escape_string($conn, $existencia);
            $campos[] = "Existencia='$existencia'";
        }

        if (empty($campos)) {
            echo "No se proporcionó ningún campo para actualizar.";
            exit();
        }

        $sql = "UPDATE $tabla SET " . implode(', ', $campos) . " WHERE ID='$id';";

        // Ejecutar la consulta
        if (mysqli_query($conn, $sql)) {
            $_SESSION['mensaje'] = "Modificación realizada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al modificar: " . mysqli_error($conn);
        }

        if ($tabla === 'producto') {
            header("Location: productos.php");
        } else {
            header("Location: materia_prima.php");
        }
    }
?>