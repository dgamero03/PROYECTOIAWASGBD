<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT id, email, password, nombre, telefono, direccion, administrador, activo 
            FROM usuarios 
            WHERE email = '$email' AND password = '$password' AND activo = 1";
    
    $resultado = $mysqli->query($sql);
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Guardar en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['telefono'] = $usuario['telefono'];
        $_SESSION['direccion'] = $usuario['direccion'];
        $_SESSION['administrador'] = $usuario['administrador'];
        $_SESSION['logueado'] = true;
        
        // Redirigir
        header('Location: index.php');
        exit();
    } else {
        $mensaje = "<div class='alert alert-danger'>Email, contraseña incorrectos o cuenta inactiva</div>";
    }
    
    $mysqli->close();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Login - Cheese Burger</title>
    <style>
        body {
            background: linear-gradient(135deg, #ff6b35 0%, #ffa500 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .logo-login {
            text-align: center;
            margin-bottom: 30px;
            color: #ff6b35;
        }
        .campo-label { 
            width: 30%;
            margin-bottom: 0; 
            font-weight: bold; 
        }
        .form-group {
            display: flex; 
            align-items: center; 
            position: relative; 
            margin-bottom: 1rem; 
        
        }
        .form-control {
            flex: 1; 
            font-size: 16px;
            width: 100%;
            margin-left: 20px;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="logo-login">
                <i class="fas fa-pizza-slice fa-3x mb-3"></i>
                <h3>Cheese Burger</h3>
                <h4 class="text-muted">Iniciar Sesión</h4>
            </div>
            
            <?php echo $mensaje; ?>
            
            <form method="POST">
                <div class="form-group d-flex align-items-center mb-3">
                    <label for="email" class="form-label campo-label"> <i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <br>
                <div class="form-group d-flex align-items-center mb-3 position-relative">
                <label for="password" class="form-label campo-label"> <i class="fas fa-lock"></i> Contraseña </label>
                <input type="password" name="password" id="password" class="form-control" required>
                <span class="position-absolute" style="right: 15px; cursor: pointer;" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
                </div>

                <br>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
                
                <div class="text-center mt-3">
                    <p class="mb-1">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                    <p><a href="index.php">Continuar como invitado</a></p>
                </div>
            </form>
        </div>
    </div>
<script>
    function togglePassword() {
    const icon = document.getElementById("toggleIcon");
    const input = document.getElementById("password");
    
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

</body>
</html>