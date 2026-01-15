<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// Verificar que el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['administrador'] != 1) {
    header('Location: index.php'); // Si no es admin, fuera
    exit();
}

// Comprobar que llega un ID por GET
if (!isset($_GET['id'])) {
    header('Location: admin_usuarios.php'); // Si no hay ID, volver al listado
    exit();
}

$id = (int)$_GET['id']; // Convertir ID a entero por seguridad

// Obtener datos del usuario a editar
$sql = "SELECT * FROM usuarios WHERE id = $id";
$resultado = $mysqli->query($sql);
$usuario = $resultado ? $resultado->fetch_assoc() : null;

// Si no existe el usuario
if (!$usuario) {
    echo "Usuario no encontrado";
    exit();
}

// Si se envió el formulario (guardar cambios)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger datos del formulario
    $nombre    = $_POST['nombre'];
    $email     = $_POST['email'];
    $telefono  = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $admin     = isset($_POST['administrador']) ? 1 : 0; // Checkbox admin

    // Actualizar usuario
    $sql_update = "
        UPDATE usuarios 
        SET nombre='$nombre', email='$email', telefono='$telefono', direccion='$direccion', administrador='$admin'
        WHERE id=$id
    ";

    // Si se actualiza correctamente
    if ($mysqli->query($sql_update)) {
        header('Location: admin_usuarios.php'); // Volver al listado
        exit();
    } else {
        echo "Error al actualizar: " . $mysqli->error;
        exit();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Usuario</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="body-editar">

<div class="editar-card"> <!-- Tarjeta principal -->

<div class="titulo-detalle text-center">
    <div class="logo-editar">
        <i class="fas fa-user-edit"></i>
        <h3>Editar Usuario</h3>

        <!-- Botón volver -->
        <a href="admin_usuarios.php" class="icono-circulo-editar icono-volver" title="Volver">
            <i class="fas fa-undo"></i>
        </a>
    </div>
</div>

    <!-- Formulario de edición -->
    <form method="POST" id="formEditar">

        <!-- Nombre -->
        <div class="form-group-editar">
            <label for="nombre" class="campo-label-editar">
                <i class="fas fa-user"></i> Nombre
            </label>
            <input type="text" name="nombre" id="nombre" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <!-- Email -->
        <div class="form-group-editar">
            <label for="email" class="campo-label-editar">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" name="email" id="email" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <!-- Teléfono -->
        <div class="form-group-editar">
            <label for="telefono" class="campo-label-editar">
                <i class="fas fa-phone"></i> Teléfono
            </label>
            <input type="text" name="telefono" id="telefono" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
        </div>

        <!-- Dirección -->
        <div class="form-group-editar">
            <label for="direccion" class="campo-label-editar">
                <i class="fas fa-map-marker-alt"></i> Dirección
            </label>
            <input type="text" name="direccion" id="direccion" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['direccion']); ?>">
        </div>

        <!-- Administrador -->
        <div class="form-group-editar">
            <label for="admin" class="campo-label-editar">
                <i class="fas fa-user-shield"></i> Administrador
            </label>

            <!-- Checkbox marcado si el usuario es admin -->
            <input type="checkbox" name="administrador" id="admin"
                <?php echo $usuario['administrador'] ? 'checked' : ''; ?>>
        </div>

    </form>

    <!-- Botón guardar -->
    <div class="guardar">
        <button type="submit" form="formEditar" class="btn-guardar">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
    </div>

</div>

</body>
</html>
