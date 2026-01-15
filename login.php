<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

$mensaje = ""; // Mensaje de error si falla el login

// Si el formulario se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger datos enviados
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Consulta para verificar usuario activo
    $sql = "SELECT id, email, password, nombre, telefono, direccion, administrador, activo 
            FROM usuarios 
            WHERE email = '$email' AND password = '$password' AND activo = 1";
    
    $resultado = $mysqli->query($sql); // Ejecutar consulta
    
    // Si existe el usuario
    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc(); // Datos del usuario
        
        // Guardar datos en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['telefono'] = $usuario['telefono'];
        $_SESSION['direccion'] = $usuario['direccion'];
        $_SESSION['administrador'] = $usuario['administrador'];
        $_SESSION['logueado'] = true;
        
        // Redirigir al inicio
        header('Location: index.php');
        exit();

    } else {
        // Error de credenciales
        $mensaje = "<div class='alert alert-danger'>Correo o contraseña incorrecta</div>";
    }
    
    $mysqli->close(); // Cerrar conexión
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <title>Login - Cheese Burger</title>
</head>

<body class="body-login"> <!-- Fondo de login -->

    <div class="container login-card"> <!-- Tarjeta principal -->

        <!-- Logo y título -->
        <div class="logo-login">
            <i class="fas fa-pizza-slice fa-3x mb-3"></i>
            <h3>Cheese Burger</h3>
            <h4 class="text-muted">Iniciar Sesión</h4>
        </div>
        
        <!-- Mensaje de error -->
        <?php echo $mensaje; ?>
        
        <!-- Formulario de login -->
        <form method="POST">

            <!-- Email -->
            <div class="form-group-login d-flex align-items-center mb-3">
                <label for="email" class="form-label campo-label-login">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" name="email" id="email" class="form-control-login" required>
            </div>

            <br>

            <!-- Contraseña con icono mostrar/ocultar -->
            <div class="form-group-login d-flex align-items-center mb-3 position-relative">
                <label for="password" class="form-label campo-label-login">
                    <i class="fas fa-lock"></i> Contraseña
                </label>

                <div class="password-wrapper"> <!-- Contenedor input + icono -->
                    <input type="password" name="password" id="password" class="form-control-login" required>

                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>

            <!-- Botones -->
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>

                <a href="index.php" class="btn btn-secondary btn-lg w-100">
                    Cancelar
                </a>
            </div>

            <!-- Enlaces inferiores -->
            <div class="text-center mt-3">
                <p class="mb-1">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <p><a href="index.php">Continuar como invitado</a></p>
            </div>

        </form>
    </div>

<!-- Script para mostrar/ocultar contraseña -->
<script>
    function togglePassword() {
        const icon = document.getElementById("toggleIcon");
        const input = document.getElementById("password");
    
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

</body>
</html>
