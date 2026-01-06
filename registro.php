<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql_check = "SELECT id FROM usuarios WHERE email = '$email'";
    $result_check = $mysqli->query($sql_check);

    if ($result_check->num_rows > 0) {
        $mensaje = "<div class='alert alert-danger'>Este email ya está registrado</div>";
    } else {
        $sql = "INSERT INTO usuarios (email, password, nombre, telefono, direccion, administrador, activo, creado_en)
                VALUES ('$email', '$password', '$nombre', '$telefono', '$direccion', 0, 1, NOW())";

        if ($mysqli->query($sql)) {
            $nuevo_id = $mysqli->insert_id;

            $_SESSION['usuario_id'] = $nuevo_id;
            $_SESSION['email'] = $email;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['telefono'] = $telefono;
            $_SESSION['direccion'] = $direccion;
            $_SESSION['administrador'] = 0;
            $_SESSION['logueado'] = true;

            header('Location: index.php');
            exit();
        } else {
            $mensaje = "<div class='alert alert-danger'>Error en el registro: " . $mysqli->error . "</div>";
        }
    }

    $mysqli->close();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro - Cheese Burger</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <!-- Estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body class="body-registro">

<div class="registro-card">
        <div class="logo-login">
            <i class="fas fa-hamburger fa-3x mb-3"></i>
            <h3>Cheese Burger</h3>
            <h4 class="text-muted">Registrarse</h4>
        </div>
    <?php echo $mensaje; ?>

    <form method="POST">
    <div class="form-group-registro d-flex align-items-center mb-3">
    <label for="nombre" class="form-label-registro campo-label-registro"><i class="fas fa-user"></i> Nombre completo </label>
    <input type="text" name="nombre" id="nombre" class="form-control-registro" required>
    </div>
<br>
    <div class="form-group-registro d-flex align-items-center mb-3">
    <label for="email" class="form-label-registro campo-label-registro"><i class="fas fa-envelope"></i> Email </label>
    <input type="email" name="email" id="email" class="form-control-registro" required>
    </div>
<br>
    <div class="form-group-registro d-flex align-items-center mb-3 position-relative">
    <label for="password" class="form-label-registro campo-label-registro"><i class="fas fa-lock"></i> Contraseña </label>
    <input type="password" name="password" id="password" class="form-control-registro" required>
    <span class="position-absolute" style="right: 15px; cursor: pointer;" onclick="togglePassword()">
        <i class="fas fa-eye" id="toggleIcon"></i>
    </span>
    </div>
<br>
    <div class="form-group-registro d-flex align-items-center mb-3">
    <label for="telefono" class="form-label-registro campo-label-registro"><i class="fas fa-phone"></i> Teléfono </label>
    <input type="tel" name="telefono" id="telefono" class="form-control-registro" required>
    </div>
<br>
    <div class="form-group-registro d-flex align-items-center mb-3">
    <label for="direccion" class="form-label-registro campo-label-registro"><i class="fas fa-home"></i> Dirección para envíos </label>
    <textarea name="direccion" id="direccion" class="form-control-registro" rows="2" required></textarea>
    </div>
<br>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">Crear Cuenta</button>
        <a href="index.php" class="btn btn-secondary btn-lg w-100">Cancelar</a>
        <p class="mt-3">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</form>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById("password");
        const icon = document.getElementById("toggleIcon");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
