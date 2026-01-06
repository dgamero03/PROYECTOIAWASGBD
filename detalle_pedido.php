<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: mis_pedidos.php');
    exit();
}

$pedido_id = (int)$_GET['id'];

$sql_pedido = "SELECT * FROM pedidos WHERE id = $pedido_id";
$resultado_pedido = $mysqli->query($sql_pedido);
$pedido = $resultado_pedido->fetch_assoc();

if (!$pedido) {
    echo "Pedido no encontrado";
    exit();
}

$sql_detalles = "
    SELECT d.*, p.nombre, p.imagen 
    FROM detalles_de_pedidos d
    INNER JOIN productos p ON d.producto_id = p.id
    WHERE d.pedido_id = $pedido_id
";

$detalles = $mysqli->query($sql_detalles);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['administrador']) && $_SESSION['administrador'] == 1) {
    $nuevo_estado = $_POST['estado'];
    $sql_update = "UPDATE pedidos SET estado = '$nuevo_estado' WHERE id = $pedido_id";
    $mysqli->query($sql_update);
    header("Location: detalle_pedido.php?id=$pedido_id");
    exit();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Detalle del Pedido</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <style>
         body {
            background-image: url('imagenes/fondopedidos.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            padding: 40px 0;
        }
        .estado-pendiente { background-color: #ffc107; color: black; }
        .estado-preparando { background-color: #17a2b8; color: white; }
        .estado-en_camino { background-color: #007bff; color: white; }
        .estado-entregado { background-color: #28a745; color: white; }

        .tarjeta-detalle {
            max-width: 700px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 25px rgba(0,0,0,0.2);
            backdrop-filter: blur(6px);
            font-size: 16px;
        }

        .card-header h3 {
            font-size: 26px;
            font-weight: 700;
        }

        .card-body h5 {
            font-size: 20px;
            font-weight: 600;
            color: #800000;
            margin-top: 25px;
        }

        .card-body p {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .producto-item {
            font-size: 15px;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
        }

        .producto-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .btn-volver {
            font-size: 15px;
            padding: 8px 16px;
        }

        .badge-estado {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        /* === ICONO VOLVER === */
        .titulo-detalle {
            position: relative;
            padding-right: 80px;
        }

        .icono-detalle {
            position: absolute;
            top: 0;
            right: 0;
        }

        .icono-circulo {
            width: 50px;
            height: 50px;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 0 6px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: 1px solid #ccc;
        }

        .icono-circulo i {
            font-size: 24px;
            color: #000;
        }
    </style>
</head>

<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="tarjeta-detalle">

        <div class="card shadow">

            <!-- === TÍTULO + ICONO DE INICIO IGUAL QUE MIS_PEDIDOS === -->
            <div class="card-header bg-primary text-white titulo-detalle">
                <h3 class="mb-0">
                    Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?>
                </h3>

                <a href="mis_pedidos.php" class="icono-circulo icono-detalle" title="Volver">
                    <i class="fas fa-undo"></i>
                </a>
            </div>

            <div class="card-body">
                <h5><i class="fas fa-info-circle"></i> Información del Pedido</h5>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?></p>
                <p><strong>Tipo:</strong> <?php echo $pedido['tipo'] == 'domicilio' ? 'A domicilio' : 'Recogida en local'; ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion']); ?></p>
                <p><strong>Total:</strong> <?php echo number_format($pedido['total'], 2); ?> €</p>
                <p><strong>Tiempo estimado:</strong> <?php echo $pedido['tiempo_estimado']; ?> minutos</p>

                <h5><i class="fas fa-box"></i> Productos</h5>
                <?php while ($item = $detalles->fetch_assoc()) { ?>
                    <div class="d-flex align-items-center producto-item bg-white">
                        <img src="<?php echo $item['imagen']; ?>">
                        <div>
                            <strong><?php echo htmlspecialchars($item['nombre']); ?></strong><br>
                            Cantidad: <?php echo $item['cantidad']; ?><br>
                            Precio: €<?php echo number_format($item['precio_unitario'], 2); ?>
                        </div>
                    </div>
                <?php } ?>

                <h5><i class="fas fa-tasks"></i> Estado del Pedido</h5>
                <?php 
                $estados = [
                    'pendiente' => 'Pendiente',
                    'preparando' => 'En preparación',
                    'en_camino' => 'En camino',
                    'entregado' => 'Entregado'
                ];

                $clase_estado = 'estado-' . str_replace(' ', '_', $pedido['estado']);
                ?>
                <p><strong>Estado actual:</strong>
                    <span class="badge-estado <?php echo $clase_estado; ?>">
                        <?php echo $estados[$pedido['estado']]; ?>
                    </span>
                </p>
                <br>
                <?php if (isset($_SESSION['administrador']) && $_SESSION['administrador'] == 1): ?>
                    <form method="POST" class="mt-3">
                        <label><strong>Cambiar estado:</strong></label>
                        <select name="estado" class="form-control mb-3">
                            <option value="pendiente" <?php if ($pedido['estado']=='pendiente') echo 'selected'; ?>>Pendiente</option>
                            <option value="preparando" <?php if ($pedido['estado']=='preparando') echo 'selected'; ?>>En preparación</option>
                            <option value="en_camino" <?php if ($pedido['estado']=='en_camino') echo 'selected'; ?>>En camino</option>
                            <option value="entregado" <?php if ($pedido['estado']=='entregado') echo 'selected'; ?>>Entregado</option>
                        </select>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
</body>
</html>

<?php $mysqli->close(); ?>
