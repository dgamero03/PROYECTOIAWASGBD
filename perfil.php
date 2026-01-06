<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = "";

// Actualizar datos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    
    $sql = "UPDATE usuarios SET 
            nombre = '$nombre',
            telefono = '$telefono',
            direccion = '$direccion'
            WHERE id = '$usuario_id'";
    
    if ($mysqli->query($sql)) {
        $_SESSION['nombre'] = $nombre;
        $_SESSION['telefono'] = $telefono;
        $_SESSION['direccion'] = $direccion;
        
        $mensaje = "<div class='alert alert-success text-center'>Datos actualizados correctamente</div>";
    } else {
        $mensaje = "<div class='alert alert-danger text-center'>Error al actualizar: " . $mysqli->error . "</div>";
    }
}

// Obtener datos actuales del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id = '$usuario_id'";
$result_usuario = $mysqli->query($sql_usuario);
$usuario = $result_usuario->fetch_assoc();
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>

<body class="body-perfil">

<div class="perfil-card">
    <a href="index.php" class="icono-circulo-perfil icono-detalle" title="Volver">
        <i class="fas fa-undo"></i>
    </a>
    <div class="logo-perfil">
        <i class="fas fa-user"></i>
        <h3>Mi Perfil</h3>
    </div>

    <?php echo $mensaje; ?>

    <form method="POST" id="formPerfil">

        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-user"></i> Nombre *</label>
            <input type="text" name="nombre" class="form-control-perfil"
                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" class="form-control-perfil"
                   value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
        </div>

        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-phone"></i> Teléfono *</label>
            <input type="tel" name="telefono" class="form-control-perfil"
                   value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
        </div>

        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-home"></i> Dirección *</label>
            <textarea name="direccion" class="form-control-perfil" rows="3" required><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
        </div>

        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-calendar"></i> Registro</label>
            <input type="text" class="form-control-perfil"
                   value="<?php echo date('d/m/Y H:i', strtotime($usuario['creado_en'])); ?>" readonly>
        </div>

    </form>

    <div class="guardar">
        <button type="submit" form="formPerfil" class="btn-perfil-guardar">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
    </div>

</div>

</body>
</html>

<?php $mysqli->close(); ?>
