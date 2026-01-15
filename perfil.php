<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// Si el usuario no está logueado, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id']; // ID del usuario logueado
$mensaje = ""; // Mensaje de éxito o error

// Si se envió el formulario (actualizar datos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger datos enviados
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    
    // Actualizar datos en la BD
    $sql = "UPDATE usuarios SET 
            nombre = '$nombre',
            telefono = '$telefono',
            direccion = '$direccion'
            WHERE id = '$usuario_id'";
    
    // Si la actualización fue correcta
    if ($mysqli->query($sql)) {

        // Actualizar datos en la sesión
        $_SESSION['nombre'] = $nombre;
        $_SESSION['telefono'] = $telefono;
        $_SESSION['direccion'] = $direccion;
        
        $mensaje = "<div class='alert alert-success text-center'>Datos actualizados correctamente</div>";

    } else {
        // Error al actualizar
        $mensaje = "<div class='alert alert-danger text-center'>Error al actualizar: " . $mysqli->error . "</div>";
    }
}

// Obtener datos actuales del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id = '$usuario_id'";
$result_usuario = $mysqli->query($sql_usuario);
$usuario = $result_usuario->fetch_assoc(); // Datos del usuario
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mi Perfil</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="body-perfil"> <!-- Fondo de perfil -->

<div class="perfil-card"> <!-- Tarjeta principal -->

    <!-- Botón volver -->
    <a href="index.php" class="icono-circulo-perfil icono-detalle" title="Volver">
        <i class="fas fa-undo"></i>
    </a>

    <!-- Icono y título -->
    <div class="logo-perfil">
        <i class="fas fa-user"></i>
        <h3>Mi Perfil</h3>
    </div>

    <!-- Mensaje de éxito o error -->
    <?php echo $mensaje; ?>

    <!-- Formulario de edición -->
    <form method="POST" id="formPerfil">

        <!-- Nombre -->
        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-user"></i> Nombre *</label>
            <input type="text" name="nombre" class="form-control-perfil"
                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <!-- Email (solo lectura) -->
        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" class="form-control-perfil"
                   value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
        </div>

        <!-- Teléfono -->
        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-phone"></i> Teléfono *</label>
            <input type="tel" name="telefono" class="form-control-perfil"
                   value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
        </div>

        <!-- Dirección -->
        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-home"></i> Dirección *</label>
            <textarea name="direccion" class="form-control-perfil" rows="3" required><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
        </div>

        <!-- Fecha de registro -->
        <div class="form-group-perfil">
            <label class="campo-label-perfil"><i class="fas fa-calendar"></i> Registro</label>
            <input type="text" class="form-control-perfil"
                   value="<?php echo date('d/m/Y H:i', strtotime($usuario['creado_en'])); ?>" readonly>
        </div>

    </form>

    <!-- Botón guardar -->
    <div class="guardar">
        <button type="submit" form="formPerfil" class="btn-perfil-guardar">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
    </div>

</div>

</body>
</html>

<?php $mysqli->close(); ?> <!-- Cerrar conexión -->
