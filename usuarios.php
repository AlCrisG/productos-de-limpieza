<?php
    session_start();
    if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
        header("Location: index.php");
        exit();
    }
    include_once 'conn.php';

    // Filtros de búsqueda
    $where = [];
    $atributos = ['ID', 'Nombre', 'ApellidoPat', 'ApellidoMat', 'Usuario', 'Rol'];
    foreach ($atributos as $atributo) {
        if (!empty($_GET[$atributo])) {
            $valor = mysqli_real_escape_string($conn, $_GET[$atributo]);
            if ($atributo === 'ID' || $atributo === 'Rol') {
                $where[] = "$atributo = '$valor'";
            } else {
                $where[] = "$atributo LIKE '%$valor%'";
            }
        }
    }

    $sql = "SELECT * FROM empleado";
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="icon" type="image/x-icon" href="icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <nav>
            <a href="welcome.php">Inicio</a>
            <a href="productos.php">Inventario</a>
            <a href="produccion.php">Producción</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="usuarios.php" class="active">Usuarios</a>
            <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                <a href="reportes.php">Reportes</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <?php
            if (isset($_SESSION['mensaje'])) {
                echo "<p style='color: lightblue; font-size:30px; padding-bottom: 30px'>" . $_SESSION['mensaje'] . "</p>";
                unset($_SESSION['mensaje']);
            }
        ?>

        <form method="get" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <input type="text" name="ID" placeholder="ID" value="<?php echo isset($_GET['ID']) ? htmlspecialchars($_GET['ID']) : ''; ?>">
            <input type="text" name="Nombre" placeholder="Nombre" value="<?php echo isset($_GET['Nombre']) ? htmlspecialchars($_GET['Nombre']) : ''; ?>">
            <input type="text" name="ApellidoPat" placeholder="Apellido Pat" value="<?php echo isset($_GET['ApellidoPat']) ? htmlspecialchars($_GET['ApellidoPat']) : ''; ?>">
            <input type="text" name="ApellidoMat" placeholder="Apellido Mat" value="<?php echo isset($_GET['ApellidoMat']) ? htmlspecialchars($_GET['ApellidoMat']) : ''; ?>">
            <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                <input type="text" name="Usuario" placeholder="Usuario" value="<?php echo isset($_GET['Usuario']) ? htmlspecialchars($_GET['Usuario']) : ''; ?>">
                <select name="Rol" style="width: auto;">
                    <option value="">Rol</option>
                    <option value="admin" <?php if(isset($_GET['Rol']) && $_GET['Rol'] === 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="administrativo" <?php if(isset($_GET['Rol']) && $_GET['Rol'] === 'administrativo') echo 'selected'; ?>>Administrativo</option>
                    <option value="empleado" <?php if(isset($_GET['Rol']) && $_GET['Rol'] === 'empleado') echo 'selected'; ?>>Empleado</option>
                </select>
            <?php endif; ?>
            <button type="submit">Buscar</button>
            <a href="usuarios.php" style="margin-left:1rem;">Limpiar</a>
        </form>

        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Fecha Ingreso</th>
                    <th>Teléfono</th>
                    <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($res && mysqli_num_rows($res) > 0) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($res)) {
                    $i++;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ApellidoPat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ApellidoMat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['FechaIngreso']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Teléfono']) . "</td>";
                    echo '<td>';
                    // Botón Ver más (modal)
                    ?>
                    <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
                        <button type="button"
                            onclick="mostrarModal(this)"
                            data-id="<?php echo htmlspecialchars($row['ID']); ?>"
                            data-nombre="<?php echo htmlspecialchars($row['Nombre']); ?>"
                            data-apellidopat="<?php echo htmlspecialchars($row['ApellidoPat']); ?>"
                            data-apellidomat="<?php echo htmlspecialchars($row['ApellidoMat']); ?>"
                            data-fechaingreso="<?php echo htmlspecialchars($row['FechaIngreso']); ?>"
                            data-fechanacimiento="<?php echo htmlspecialchars($row['FechaNacimiento']); ?>"
                            data-lugarnacimiento="<?php echo htmlspecialchars($row['LugarNacimiento']); ?>"
                            data-calle="<?php echo htmlspecialchars($row['Calle']); ?>"
                            data-colonia="<?php echo htmlspecialchars($row['Colonia']); ?>"
                            data-ciudad="<?php echo htmlspecialchars($row['Ciudad']); ?>"
                            data-telefono="<?php echo htmlspecialchars($row['Teléfono']); ?>"
                            data-rol="<?php echo htmlspecialchars($row['Rol']); ?>"
                            data-usuario="<?php echo !empty($row['Usuario']) ? htmlspecialchars($row['Usuario']) : '-'; ?>"
                        >Ver más</button>
                    <?php endif; ?>
                    <?php
                    // Botones Editar y Eliminar solo para admin
                    if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin') {
                        echo ' <a href="modificar_usuario.php?id=' . urlencode($row['ID']) . '" class="contrast">Editar</a>';
                        echo ' <a href="eliminar_usuario.php?id=' . urlencode($row['ID']) . '" class="secondary"
                                onclick="return confirm(\'¿Seguro que deseas eliminar este usuario?\')" style="margin-left:10px;">Eliminar</a>';
                    }
                    echo '</td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No hay empleados registrados.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        </div>

        <!-- Modal para mostrar detalles -->
        <div id="modalDetalles" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#2c3e50; color:#222; padding:2rem; border-radius:10px; min-width:300px; max-width:90vw; max-height:90vh; overflow:auto; position:relative;">
                <button onclick="cerrarModal()" style="position:absolute; top:10px; right:10px;">X</button>
                <div id="contenidoModal"></div>
            </div>
        </div>

        <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'admin'): ?>
        <section style="margin-top:3rem;">
            <h3>Registrar nuevo empleado</h3>
            <form method="post" action="registrar_usuario.php" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
                <input type="text" maxlength="20" name="Nombre" placeholder="Nombre" required>
                <input type="text" maxlength="20" name="ApellidoPat" placeholder="Apellido Paterno" required>
                <input type="text" maxlength="20" name="ApellidoMat" placeholder="Apellido Materno" required>
                <p>Fecha de Ingreso</p>
                <input type="date" name="FechaIngreso" placeholder="Fecha de Ingreso" required>
                <p>Fecha de Nacimiento</p>
                <input type="date" name="FechaNacimiento" placeholder="Fecha de Nacimiento" required>
                <input type="text" maxlength="45" name="LugarNacimiento" placeholder="Lugar de Nacimiento" required>
                <input type="text" maxlength="45" name="Calle" placeholder="Calle" required>
                <input type="text" maxlength="30" name="Colonia" placeholder="Colonia" required>
                <input type="text" maxlength="20" name="Ciudad" placeholder="Ciudad" required>
                <input type="text" maxlength="10" name="Telefono" placeholder="Teléfono" required>
                <select name="Rol" id="rolSelect" required>
                    <option value="">Selecciona rol</option>
                    <option value="admin">Admin</option>
                    <option value="administrativo">Administrativo</option>
                    <option value="empleado">Empleado</option>
                </select>
                <input type="text" maxlength="20" name="Usuario" id="usuarioInput" placeholder="Usuario" style="display:none;">
                <input type="password" maxlength="16" name="Contrasena" id="contrasenaInput" placeholder="Contraseña" style="display:none;">
                <button type="submit">Registrar</button>
            </form>
        </section>
        <?php endif; ?>
    </main>
    <script>
    document.getElementById('rolSelect').addEventListener('change', function() {
        var rol = this.value;
        var usuario = document.getElementById('usuarioInput');
        var contrasena = document.getElementById('contrasenaInput');
        if (rol === 'admin' || rol === 'administrativo') {
            usuario.style.display = '';
            contrasena.style.display = '';
            usuario.required = true;
            contrasena.required = true;
        } else {
            usuario.style.display = 'none';
            contrasena.style.display = 'none';
            usuario.required = false;
            contrasena.required = false;
        }
    });

    function mostrarModal(btn) {
        var contenido = `
            <div style="color: #fff;">
                <h3>Detalles del Usuario</h3>
                <strong>ID:</strong> ${btn.dataset.id}<br>
                <strong>Nombre:</strong> ${btn.dataset.nombre}<br>
                <strong>Apellido Paterno:</strong> ${btn.dataset.apellidopat}<br>
                <strong>Apellido Materno:</strong> ${btn.dataset.apellidomat}<br>
                <strong>Fecha Ingreso:</strong> ${btn.dataset.fechaingreso}<br>
                <strong>Fecha Nacimiento:</strong> ${btn.dataset.fechanacimiento}<br>
                <strong>Lugar Nacimiento:</strong> ${btn.dataset.lugarnacimiento}<br>
                <strong>Domicilio:</strong> ${btn.dataset.calle}, ${btn.dataset.colonia}, ${btn.dataset.ciudad}<br>
                <strong>Teléfono:</strong> ${btn.dataset.telefono}<br>
                <strong>Rol:</strong> ${btn.dataset.rol}<br>
                <strong>Usuario:</strong> ${btn.dataset.usuario}
            </div>
        `;
        document.getElementById('contenidoModal').innerHTML = contenido;
        document.getElementById('modalDetalles').style.display = 'flex';
    }
    function cerrarModal() {
        document.getElementById('modalDetalles').style.display = 'none';
    }
    </script>
</body>
</html>