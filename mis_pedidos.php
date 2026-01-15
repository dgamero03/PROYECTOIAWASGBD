<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// Si el usuario no está logueado, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id']; // ID del usuario logueado

// Consulta de pedidos del usuario + total de productos por pedido
$sql = "SELECT p.*, 
               (SELECT SUM(cantidad) FROM detalles_de_pedidos WHERE pedido_id = p.id) as total_productos
        FROM pedidos p 
        WHERE p.usuario_id = '$usuario_id' 
        ORDER BY p.creado_en DESC";

$resultado = $mysqli->query($sql); // Ejecutar consulta
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

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mis Pedidos - Cheese Burger</title>
</head> 

<body class="body-pedidos"> <!-- Fondo de pedidos -->

    <div class="container">
        <div class="tarjeta-centro"> <!-- Tarjeta principal -->

            <!-- Título + icono de inicio -->
            <div class="titulo-pedidos mb-4">
                <h1 class="mb-0"><i class="fas fa-history"></i> Mis Pedidos</h1>

                <!-- Botón volver al inicio -->
                <a href="index.php" class="icono-circulo-pedidos icono-inicio-pedidos icono-pedidos" title="Inicio">
                    <i class="fas fa-home"></i>
                </a>
            </div>

            <!-- Si hay pedidos -->
            <?php if ($resultado->num_rows > 0): ?>

                <?php while($pedido = $resultado->fetch_assoc()): 
                    // Clase CSS según estado del pedido
                    $clase_estado = 'estado-' . str_replace(' ', '_', $pedido['estado'] ?? 'pendiente');
                ?>

                <!-- Tarjeta de cada pedido -->
                <div class="card card-pedido">

                    <!-- Cabecera del pedido -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?>
                        </h5>

                        <!-- Estado del pedido -->
                        <span class="badge badge-estado <?php echo $clase_estado; ?>">
                            <?php 
                            $estados = [
                                'pendiente' => 'Pendiente',
                                'preparando' => 'En preparación',
                                'en_camino' => 'En camino',
                                'entregado' => 'Entregado'
                            ];
                            echo $estados[$pedido['estado'] ?? 'pendiente'];
                            ?>
                        </span>
                    </div>
                    
                    <!-- Cuerpo del pedido -->
                    <div class="card-body">
                        <div class="row">

                            <!-- Columna izquierda -->
                            <div class="col-6">
                                <p class="mb-1">
                                    <i class="fas fa-calendar"></i> 
                                    <strong>Fecha:</strong><br>
                                    <?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?>
                                </p>

                                <p class="mb-1">
                                    <i class="fas fa-<?php echo $pedido['tipo'] == 'domicilio' ? 'truck' : 'store'; ?>"></i>
                                    <strong>Tipo:</strong><br>
                                    <?php echo $pedido['tipo'] == 'domicilio' ? 'A domicilio' : 'Recogida'; ?>
                                </p>
                            </div>

                            <!-- Columna derecha -->
                            <div class="col-6">
                                <p class="mb-1">
                                    <i class="fas fa-box"></i>
                                    <strong>Productos:</strong><br>
                                    Unidades: <?php echo $pedido['total_productos'] ?: 0; ?> 
                                </p>

                                <p class="mb-1">
                                    <i class="fas fa-euro-sign"></i>
                                    <strong>Total:</strong><br>
                                    €<?php echo number_format($pedido['total'], 2); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Dirección si es domicilio -->
                        <?php if ($pedido['tipo'] == 'domicilio' && $pedido['direccion']): ?>
                        <div class="mt-1">
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong>Dirección:</strong><br>
                                <?php echo htmlspecialchars($pedido['direccion']); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <!-- Tiempo estimado -->
                        <?php if ($pedido['tiempo_estimado']): ?>
                        <div class="mt-3">
                            <p class="mb-0">
                                <i class="fas fa-clock"></i>
                                <strong>Tiempo estimado:</strong> 
                                <?php echo $pedido['tiempo_estimado']; ?> minutos
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botón ver detalles -->
                    <div class="card-footer text-center">
                        <a href="detalle_pedido.php?id=<?php echo $pedido['id']; ?>" 
                           class="btn btn-primary btn-sm px-4 py-2">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </div>

                </div> <!-- Fin tarjeta pedido -->

                <?php endwhile; ?>

            <!-- Si no hay pedidos -->
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
                    <h3>Aún no has realizado pedidos</h3>
                    <p class="text-muted">Haz tu primer pedido y aparecerá aquí</p>

                    <a href="menu_completo.php" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-book"></i> Ver Carta
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>

<?php $mysqli->close(); ?> <!-- Cerrar conexión -->
