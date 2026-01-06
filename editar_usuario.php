<?php
session_start();
require 'conexion.php';

// Verificar admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['administrador'] != 1) {
    header('Location: index.php');
    exit();
}

// Comprobar que llega id
if (!isset($_GET['id'])) {
    header('Location: admin_usuarios.php');
    exit();
}

$id = (int)$_GET['id'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = $id";
$resultado = $mysqli->query($sql);
$usuario = $resultado ? $resultado->fetch_assoc() : null;

if (!$usuario) {
    echo "Usuario no encontrado";
    exit();
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = $_POST['nombre'];
    $email     = $_POST['email'];
    $telefono  = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $admin     = isset($_POST['administrador']) ? 1 : 0;

    $sql_update = "
        UPDATE usuarios 
        SET nombre='$nombre', email='$email', telefono='$telefono', direccion='$direccion', administrador='$admin'
        WHERE id=$id
    ";

    if ($mysqli->query($sql_update)) {
        header('Location: admin_usuarios.php');
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
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>

<body class="body-editar">

<div class="editar-card">

<div class="titulo-detalle text-center">
    <div class="logo-editar">
        <i class="fas fa-user-edit"></i>
        <h3>Editar Usuario</h3>
        <a href="admin_usuarios.php" class="icono-circulo-editar icono-detalle" title="Volver">
            <i class="fas fa-undo"></i>
        </a>
    </div>
</div>

    <form method="POST" id="formEditar">

        <div class="form-group-editar">
            <label for="nombre" class="campo-label-editar">
                <i class="fas fa-user"></i> Nombre
            </label>
            <input type="text" name="nombre" id="nombre" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <div class="form-group-editar">
            <label for="email" class="campo-label-editar">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" name="email" id="email" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <div class="form-group-editar">
            <label for="telefono" class="campo-label-editar">
                <i class="fas fa-phone"></i> Teléfono
            </label>
            <input type="text" name="telefono" id="telefono" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
        </div>

        <div class="form-group-editar">
            <label for="direccion" class="campo-label-editar">
                <i class="fas fa-map-marker-alt"></i> Dirección
            </label>
            <input type="text" name="direccion" id="direccion" class="form-control-editar"
                value="<?php echo htmlspecialchars($usuario['direccion']); ?>">
        </div>

        <div class="form-group-editar">
            <label for="admin" class="campo-label-editar">
                <i class="fas fa-user-shield"></i> Administrador
            </label>
            <input type="checkbox" name="administrador" id="admin"
                <?php echo $usuario['administrador'] ? 'checked' : ''; ?>>
        </div>

    </form>

    <div class="guardar">
        <button type="submit" form="formEditar" class="btn-guardar">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
    </div>

</div>

</body>
</html>
