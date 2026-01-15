<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

$mensaje = ""; // Variable para mostrar errores o avisos

// Si el formulario se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Comprobar si el email ya existe
    $sql_check = "SELECT id FROM usuarios WHERE email = '$email'";
    $result_check = $mysqli->query($sql_check);

    // Si ya existe, mostrar error
    if ($result_check->num_rows > 0) {
        $mensaje = "<div class='alert alert-danger'>Este email ya está registrado</div>";
    } else {

        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (email, password, nombre, telefono, direccion, administrador, activo, creado_en)
                VALUES ('$email', '$password', '$nombre', '$telefono', '$direccion', 0, 1, NOW())";

        // Si se inserta correctamente
        if ($mysqli->query($sql)) {

            $nuevo_id = $mysqli->insert_id; // ID del nuevo usuario

            // Guardar datos en la sesión
            $_SESSION['usuario_id'] = $nuevo_id;
            $_SESSION['email'] = $email;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['telefono'] = $telefono;
            $_SESSION['direccion'] = $direccion;
            $_SESSION['administrador'] = 0;
            $_SESSION['logueado'] = true;

            // Redirigir al inicio
            header('Location: index.php');
            exit();

        } else {
            // Error al insertar
            $mensaje = "<div class='alert alert-danger'>Error en el registro: " . $mysqli->error . "</div>";
        }
    }

    $mysqli->close(); // Cerrar conexión
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"> <!-- Codificación -->
    <title>Registro - Cheese Burger</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Iconos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>"> <!-- Tus estilos -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive -->
</head>

<body class="body-registro"> <!-- Fondo de registro -->

<div class="registro-card"> <!-- Tarjeta principal -->

    <!-- Logo y título -->
    <div class="logo-login">
        <i class="fas fa-hamburger fa-3x mb-3"></i>
        <h3>Cheese Burger</h3>
        <h4 class="text-muted">Registrarse</h4>
    </div>

    <!-- Mensaje de error o éxito -->
    <?php echo $mensaje; ?>

    <!-- Formulario de registro -->
    <form method="POST">

        <!-- Nombre -->
        <div class="form-group-registro d-flex align-items-center mb-3">
            <label for="nombre" class="form-label-registro campo-label-registro">
                <i class="fas fa-user"></i> Nombre completo
            </label>
            <input type="text" name="nombre" id="nombre" class="form-control-registro" required>
        </div>

        <br>

        <!-- Email -->
        <div class="form-group-registro d-flex align-items-center mb-3">
            <label for="email" class="form-label-registro campo-label-registro">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" name="email" id="email" class="form-control-registro" required>
        </div>

        <br>

        <!-- Contraseña con icono mostrar/ocultar -->
        <div class="form-group-registro d-flex align-items-center mb-3 position-relative">
            <label for="password" class="form-label-registro campo-label-registro">
                <i class="fas fa-lock"></i> Contraseña
            </label>

            <div class="password-wrapper"> <!-- Contenedor del input + icono -->
                <input type="password" name="password" id="password" class="form-control-registro" required>

                <!-- Icono para mostrar/ocultar -->
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div>
        </div>

        <br>

        <!-- Teléfono -->
        <div class="form-group-registro d-flex align-items-center mb-3">
            <label for="telefono" class="form-label-registro campo-label-registro">
                <i class="fas fa-phone"></i> Teléfono
            </label>
            <input type="tel" name="telefono" id="telefono" class="form-control-registro" required>
        </div>

        <br>

        <!-- Dirección -->
        <div class="form-group-registro d-flex align-items-center mb-3">
            <label for="direccion" class="form-label-registro campo-label-registro">
                <i class="fas fa-home"></i> Dirección para envíos
            </label>
            <textarea name="direccion" id="direccion" class="form-control-registro" rows="2" required></textarea>
        </div>

        <br>

        <!-- Botones -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">Crear Cuenta</button>
            <a href="index.php" class="btn btn-secondary btn-lg w-100">Cancelar</a>

            <p class="mt-3">
                ¿Ya tienes cuenta?
                <a href="login.php">Inicia sesión aquí</a>
            </p>
        </div>

    </form>
</div>

<!-- Script para mostrar/ocultar contraseña -->
<script>
    function togglePassword() {
        const input = document.getElementById("password");
        const icon = document.getElementById("toggleIcon");

        if (input.type === "password") {
            input.type = "text"; // Mostrar
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password"; // Ocultar
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
