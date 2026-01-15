<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// ===============================
// VERIFICAR QUE EL USUARIO ES ADMINISTRADOR
// ===============================
if (!isset($_SESSION['administrador']) || $_SESSION['administrador'] != 1) {
    header('Location: index.php'); // Si no es admin, fuera
    exit();
}

// ===============================
// CONSULTAS DE ESTADÍSTICAS
// ===============================
$sql_estadisticas = [
    'total_pedidos' => "SELECT COUNT(*) as total FROM pedidos",
    'pedidos_hoy' => "SELECT COUNT(*) as total FROM pedidos WHERE DATE(creado_en) = CURDATE()",
    'total_usuarios' => "SELECT COUNT(*) as total FROM usuarios",
    'total_productos' => "SELECT COUNT(*) as total FROM productos WHERE activo = 1",
    'ingresos_hoy' => "SELECT COALESCE(SUM(total), 0) as total FROM pedidos WHERE DATE(creado_en) = CURDATE()"
];

// Ejecutar todas las consultas y guardar resultados
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

    <!-- Vista adaptable -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <title>Panel de Administración - Cheese Burger</title>
</head>

<body>

    <!-- ===============================
         CABECERA DEL PANEL ADMIN
    =============================== -->
    <div class="admin-header contenedor-admin">
        <div class="container">
            <div class="row align-items-center panel-admin">

                <!-- Título y bienvenida -->
                <div class="col-md-8">
                    <h1 class="mb-3"><i class="fas fa-cogs"></i> Panel de Administración</h1>
                    <p class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                </div>

                <!-- Botones de navegación -->
                <div class="col-md-4 d-flex justify-content-center justify-content-md-end flex-wrap gap-3">
                    <a href="index.php" class="btn btn-inicio">
                        <i class="fas fa-home"></i> Volver al Inicio
                    </a>

                    <a href="logout.php" class="btn btn-danger btn-cerrar">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- ===============================
         MÓDULOS DEL PANEL ADMIN
    =============================== -->
    <div class="container contenedor-admin">

        <h4 class="mb-4"><i class="fas fa-th-large"></i> Módulos</h4>

        <div class="row">

            <!-- Gestión de productos -->
            <div class="col-12 col-sm-12 col-lg-4">
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

            <!-- Gestión de pedidos -->
            <div class="col-12 col-sm-12 col-lg-4">
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

            <!-- Gestión de usuarios -->
            <div class="col-12 col-sm-12 col-lg-4">
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

        <!-- ===============================
             ESTADÍSTICAS DEL SISTEMA
        =============================== -->
        <h4 class="mb-4"><i class="fas fa-chart-bar"></i> Estadísticas</h4>

        <div class="row mb-5">

            <!-- Pedidos hoy -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card-stat stat-pedidos">
                    <div class="text-center">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <h2><?php echo $estadisticas['pedidos_hoy']; ?></h2>
                        <p class="mb-0">Pedidos Hoy</p>
                    </div>
                </div>
            </div>

            <!-- Ingresos hoy -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card-stat stat-ingresos">
                    <div class="text-center">
                        <i class="fas fa-euro-sign fa-3x mb-3"></i>
                        <h2><?php echo number_format($estadisticas['ingresos_hoy'], 2); ?> €</h2>
                        <p class="mb-0">Ingresos Hoy</p>
                    </div>
                </div>
            </div>

            <!-- Productos activos -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card-stat stat-productos">
                    <div class="text-center">
                        <i class="fas fa-pizza-slice fa-3x mb-3"></i>
                        <h2><?php echo $estadisticas['total_productos']; ?></h2>
                        <p class="mb-0">Productos Activos</p>
                    </div>
                </div>
            </div>

            <!-- Total usuarios -->
            <div class="col-6 col-lg-3 mb-4">
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

    <!-- Scripts -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>

<?php $mysqli->close(); ?> <!-- Cerrar conexión -->
