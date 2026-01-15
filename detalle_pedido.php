<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// ===============================
// VERIFICAR LOGIN
// ===============================
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// ===============================
// VERIFICAR QUE LLEGA UN ID
// ===============================
if (!isset($_GET['id'])) {
    header('Location: mis_pedidos.php');
    exit();
}

$pedido_id = (int)$_GET['id']; // ID del pedido convertido a entero

// ===============================
// OBTENER DATOS DEL PEDIDO
// ===============================
$sql_pedido = "SELECT * FROM pedidos WHERE id = $pedido_id";
$resultado_pedido = $mysqli->query($sql_pedido);
$pedido = $resultado_pedido->fetch_assoc();

// Si no existe el pedido
if (!$pedido) {
    echo "Pedido no encontrado";
    exit();
}

// ===============================
// CONTROL DE ACCESO
// ===============================
// Si NO es admin y el pedido NO es suyo → fuera
if ($_SESSION['administrador'] != 1 && $pedido['usuario_id'] != $_SESSION['usuario_id']) {
    header('Location: mis_pedidos.php');
    exit();
}

// ===============================
// OBTENER PRODUCTOS DEL PEDIDO
// ===============================
$sql_detalles = "
    SELECT d.*, p.nombre, p.imagen 
    FROM detalles_de_pedidos d
    INNER JOIN productos p ON d.producto_id = p.id
    WHERE d.pedido_id = $pedido_id
";

$detalles = $mysqli->query($sql_detalles);

// ===============================
// CAMBIAR ESTADO (solo admin)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['administrador'] == 1) {
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

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="body-detalle-pedido">

<div class="container contenedor-detalle mt-4 mb-5">
    <div class="tarjeta-detalle">
        <div class="card shadow">

            <!-- Cabecera del pedido -->
            <div class="card-header bg-primary text-white titulo-detalle">
                <h3 class="mb-0">
                    Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?>
                </h3>

                <!-- Botón volver -->
                <a href="mis_pedidos.php" class="icono-circulo-pedido icono-detalle" title="Volver">
                    <i class="fas fa-undo"></i>
                </a>
            </div>

            <div class="card-body">

                <!-- Información general del pedido -->
                <h5><i class="fas fa-info-circle"></i> Información del Pedido</h5>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?></p>
                <p><strong>Tipo:</strong> <?php echo $pedido['tipo'] == 'domicilio' ? 'A domicilio' : 'Recogida en local'; ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion']); ?></p>
                <p><strong>Total:</strong> <?php echo number_format($pedido['total'], 2); ?> €</p>
                <p><strong>Tiempo estimado:</strong> <?php echo $pedido['tiempo_estimado']; ?> minutos</p>

                <!-- Productos del pedido -->
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

                <!-- Estado del pedido -->
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

                <!-- Formulario para cambiar estado (solo admin) -->
                <?php if ($_SESSION['administrador'] == 1): ?>
                    <form method="POST" class="mt-3">
                        <label><strong>Cambiar estado:</strong></label>

                        <select name="estado" class="form-control mb-3">
                            <option value="pendiente"   <?php if ($pedido['estado']=='pendiente') echo 'selected'; ?>>Pendiente</option>
                            <option value="preparando"  <?php if ($pedido['estado']=='preparando') echo 'selected'; ?>>En preparación</option>
                            <option value="en_camino"   <?php if ($pedido['estado']=='en_camino') echo 'selected'; ?>>En camino</option>
                            <option value="entregado"   <?php if ($pedido['estado']=='entregado') echo 'selected'; ?>>Entregado</option>
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

<?php $mysqli->close(); ?> <!-- Cerrar conexión -->
