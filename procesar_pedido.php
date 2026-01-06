<?php
session_start();
require 'conexion.php';

// Verificar que el usuario está logueado y tiene carrito
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $nombre_cliente = $_SESSION['nombre'];
    $telefono = $_SESSION['telefono'];
    $tipo = $_POST['tipo'];
    $direccion = ($tipo == 'domicilio') ? $_SESSION['direccion'] : 'Recogida en local';
    
    // Calcular total y tiempo máximo
    $total = 0;
    $tiempo_maximo = 0;
    
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
        $tiempo_maximo = max($tiempo_maximo, $item['tiempo_preparacion']);
    }
    
    // Añadir coste de envío si es domicilio y total < 15
    if ($tipo == 'domicilio' && $total < 15) {
        $total += 2.50;
    }
    
    $tiempo_estimado = $tiempo_maximo + 15; // +15 minutos para envío/preparación
    
    // 1. Insertar pedido en tabla pedidos
   $sql_pedido = "INSERT INTO pedidos (usuario_id, nombre_cliente, telefono, tipo, direccion, tiempo_estimado, total, estado) 
               VALUES ('$usuario_id', '$nombre_cliente', '$telefono', '$tipo', '$direccion', '$tiempo_estimado', '$total', 'pendiente')";

    
    if ($mysqli->query($sql_pedido)) {
        $pedido_id = $mysqli->insert_id;
        
        // 2. Insertar detalles del pedido
        foreach ($_SESSION['carrito'] as $item) {
            $producto_id = $item['id'];
            $cantidad = $item['cantidad'];
            $precio_unitario = $item['precio'];
            $tiempo_preparacion_producto = $item['tiempo_preparacion'];
            
            $sql_detalle = "INSERT INTO detalles_de_pedidos (pedido_id, producto_id, cantidad, precio_unitario, tiempo_preparacion_producto) 
                           VALUES ('$pedido_id', '$producto_id', '$cantidad', '$precio_unitario', '$tiempo_preparacion_producto')";
            $mysqli->query($sql_detalle);
        }
        
        // 3. Vaciar carrito
        $_SESSION['carrito'] = [];
        
        // 4. Mostrar confirmación
        ?>
        <!doctype html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <title>Pedido Confirmado - Cheese Burger</title>
            <style>
                .confirmacion {
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    padding: 50px;
                    border-radius: 15px;
                    text-align: center;
                    margin: 100px auto;
                    max-width: 600px;
                }
                .numero-pedido {
                    font-size: 3rem;
                    font-weight: bold;
                    color: #ffeb3b;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="confirmacion">
                    <i class="fas fa-check-circle fa-4x mb-4"></i>
                    <h1>¡Pedido Confirmado!</h1>
                    
                    <div class="mt-4 mb-4">
                        <h3>Número de Pedido</h3>
                        <div class="numero-pedido">#<?php echo str_pad($pedido_id, 6, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    
                    <div class="alert alert-light text-dark mt-4">
                        <h5><i class="fas fa-info-circle"></i> Información del Pedido</h5>
                        <p class="mb-1"><strong>Tipo:</strong> <?php echo $tipo == 'domicilio' ? 'A domicilio' : 'Recogida en local'; ?></p>
                        <p class="mb-1"><strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?></p>
                        <p class="mb-1"><strong>Total:</strong> €<?php echo number_format($total, 2); ?></p>
                        <p class="mb-1"><strong>Tiempo estimado:</strong> <?php echo $tiempo_estimado; ?> minutos</p>
                        <p class="mb-0"><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="mis_pedidos.php" class="btn btn-light btn-lg mr-3">
                            <i class="fas fa-list"></i> Ver Mis Pedidos
                        </a>
                        <a href="index.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Error al procesar el pedido: " . $mysqli->error;
    }
} else {
    header('Location: carrito.php');
}

$mysqli->close();
?>