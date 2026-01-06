<?php
session_start();
require 'conexion.php';

// Verificar que es administrador
if (!isset($_SESSION['administrador']) || $_SESSION['administrador'] != 1) {
    header('Location: index.php');
    exit();
}

// Estadísticas
$sql_estadisticas = [
    'total_pedidos' => "SELECT COUNT(*) as total FROM pedidos",
    'pedidos_hoy' => "SELECT COUNT(*) as total FROM pedidos WHERE DATE(creado_en) = CURDATE()",
    'total_usuarios' => "SELECT COUNT(*) as total FROM usuarios",
    'total_productos' => "SELECT COUNT(*) as total FROM productos WHERE activo = 1",
    'ingresos_hoy' => "SELECT COALESCE(SUM(total), 0) as total FROM pedidos WHERE DATE(creado_en) = CURDATE()"
];

$estadisticas = [];
foreach ($sql_estadisticas as $key => $sql) {
    $result = $mysqli->query($sql);
    $estadisticas[$key] = $result->fetch_assoc()['total'];
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
    <title>Panel de Administración - Cheese Burger</title>
</head>
<body>
    <!-- CABECERA ADMIN -->
    <div class="admin-header contenedor-admin">
        <div class="container">
            <div class="row align-items-center panel-admin">
                <div class="col-md-8">
                    <h1><i class="fas fa-cogs"></i> Panel de Administración</h1>
                    <p class="mb-0">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                </div>
                <br>
                <div class="col-md-4 text-end">
                    <a href="index.php" class="btn btn-success btn-sm me-2">
                        <i class="fas fa-home"></i> Volver al Inicio
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container contenedor-admin">
         <!-- MÓDULOS DE ADMINISTRACIÓN -->
        <h4 class="mb-4"><i class="fas fa-th-large"></i> Módulos</h4>
        <div class="row">
            <!-- GESTIÓN DE PRODUCTOS -->
            <div class="col-md-4">
            <div class="card-modulo">
                <a href="anadir_productos.php" class="btn-modulo">
                <div class="text-center mb-4">
                    <i class="fas fa-utensils fa-3x"></i>
                </div>
                <h4 class="text-center">Gestión de Productos</h4>
                <p class="text-center text-muted">Añade productos al menú</p>  
                </a>
            </div>
            </div>

            
            <!-- GESTIÓN DE PEDIDOS -->
            <div class="col-md-4">
                <div class="card-modulo">
                    <a href="mis_pedidos.php" class="btn-modulo">
                        <div class="text-center mb-4">
                            <i class="fas fa-clipboard-list fa-3x"></i>
                        </div>
                        <h4 class="text-center">Gestión de Pedidos</h4>
                        <p class="text-center text-muted">Estado de los pedidos recibidos</p>
                    </a>
                </div>
            </div>

            
            <!-- GESTIÓN DE USUARIOS -->
            <div class="col-md-4">
                <div class="card-modulo">
                    <a href="admin_usuarios.php" class="btn-modulo">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-cog fa-3x"></i>
                        </div>
                        <h4 class="text-center">Gestión de Usuarios</h4>
                        <p class="text-center text-muted">Administra usuarios y permisos</p>
                    </a>
                </div>
            </div>
        </div>
        <br>
        <!-- ESTADÍSTICAS -->
        <h4 class="mb-4"><i class="fas fa-chart-bar"></i> Estadísticas</h4>
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card-stat stat-pedidos">
                    <div class="text-center">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <h2><?php echo $estadisticas['pedidos_hoy']; ?></h2>
                        <p class="mb-0">Pedidos Hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stat stat-ingresos">
                    <div class="text-center">
                        <i class="fas fa-euro-sign fa-3x mb-3"></i>
                        <h2><?php echo number_format($estadisticas['ingresos_hoy'], 2); ?> €</h2>
                        <p class="mb-0">Ingresos Hoy</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-stat stat-productos">
                    <div class="text-center">
                        <i class="fas fa-pizza-slice fa-3x mb-3"></i>
                        <h2><?php echo $estadisticas['total_productos']; ?></h2>
                        <p class="mb-0">Productos Activos</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                    <div class="card-stat stat-usuarios">
                        <div class="text-center">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h2><?php echo $estadisticas['total_usuarios']; ?></h2>
                            <p class="mb-0">Total Usuarios</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>

<?php $mysqli->close(); ?>