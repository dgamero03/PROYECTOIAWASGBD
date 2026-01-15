<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// ===============================
// VERIFICAR QUE EL USUARIO ES ADMINISTRADOR
// ===============================
if (!isset($_SESSION['usuario_id']) || $_SESSION['administrador'] != 1) {
    header('Location: index.php'); // Si no es admin, fuera
    exit();
}

// ===============================
// CONSULTA PARA OBTENER TODOS LOS USUARIOS
// ===============================
$sql = "SELECT id, nombre, email, telefono, direccion, administrador 
        FROM usuarios 
        ORDER BY id ASC";

$resultado = $mysqli->query($sql);

// Si falla la consulta
if (!$resultado) {
    echo "<div class='alert alert-danger text-center'>
            Error al cargar usuarios: " . $mysqli->error . "
          </div>";
    exit();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Usuarios</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="body-admin-usuarios">

<div class="admin-card">

    <!-- Botón volver -->
    <a href="admin_panel.php" class="icono-circulo-admin icono-detalle" title="Volver">
        <i class="fas fa-undo"></i>
    </a>

    <!-- Título -->
    <div class="titulo-detalle text-center">
        <div class="bloque-gestion-titulo">
            <div class="icono-admin">
                <i class="fas fa-users-cog"></i>
            </div>
            <h2 class="mt-3">Gestión de Usuarios</h2>
        </div>    
    </div>

    <!-- ===============================
         TABLA DE USUARIOS
    =============================== -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center tabla-centro">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Permisos</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($u = $resultado->fetch_assoc()) { ?>
                <tr>

                    <!-- ID -->
                    <td><?php echo $u['id']; ?></td>

                    <!-- Nombre -->
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>

                    <!-- Email -->
                    <td><?php echo htmlspecialchars($u['email']); ?></td>

                    <!-- Teléfono -->
                    <td><?php echo htmlspecialchars($u['telefono']); ?></td>

                    <!-- Dirección -->
                    <td><?php echo htmlspecialchars($u['direccion']); ?></td>

                    <!-- Permisos -->
                    <td>
                        <?php echo $u['administrador'] ? 'Administrador' : 'Usuario'; ?>
                    </td>

                    <!-- Botón editar -->
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" 
                           class="btn btn-editar btn-sm badge-estado">
                            <i class="fas fa-pen"></i> Editar
                        </a>
                    </td>

                </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>

</div>

</body>
</html>
