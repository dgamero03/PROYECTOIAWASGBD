<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// Verificar si hay usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?redirect=carrito'); // Redirige si no está logueado
    exit();
}

// ===============================
// PROCESAR ACCIONES DEL CARRITO
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vaciar carrito
    if (isset($_POST['vaciar'])) {
        $_SESSION['carrito'] = [];

    // Eliminar un producto
    } elseif (isset($_POST['eliminar'])) {
        $id_eliminar = $_POST['eliminar_id'];

        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] == $id_eliminar) {
                unset($_SESSION['carrito'][$key]);
                break;
            }
        }

        // Reindexar array
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);

    // Actualizar cantidad
    } elseif (isset($_POST['actualizar'])) {
        $id_actualizar = $_POST['actualizar_id'];
        $nueva_cantidad = max(1, (int)$_POST['nueva_cantidad']);

        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $id_actualizar) {
                $item['cantidad'] = $nueva_cantidad;
                break;
            }
        }
        unset($item); // Buenas prácticas al usar referencias
    }
}

// ===============================
// CALCULAR TOTALES
// ===============================
$total = 0;
$tiempo_total = 0;
$carrito_vacio = empty($_SESSION['carrito']);

if (!$carrito_vacio) {
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad']; // Suma total
        $tiempo_total = max($tiempo_total, $item['tiempo_preparacion']); // Tiempo más largo
    }
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

    <!-- Estilos propios -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <title>Carrito - Cheese Burger</title>
</head>

<body class="body-carrito">

    <div class="container">

        <!-- ===============================
             CABECERA DEL CARRITO
        =============================== -->
        <div class="carrito-header">
            <div class="row align-items-center">

                <div class="col-md-8">
                    <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
                    <p class="mb-0">Usuario: <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                </div>

                <div class="col-md-4 text-end">
                    <a href="carta.php" class="btn btn-light">
                        <i class="fas fa-book"></i> Volver a la carta
                    </a>
                </div>

            </div>
        </div>
        
        <!-- ===============================
             SI EL CARRITO ESTÁ VACÍO
        =============================== -->
        <?php if ($carrito_vacio): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h3>Tu carrito está vacío</h3>
            <p class="text-muted">Añade productos para continuar</p>
        </div>

        <?php else: ?>

        <div class="row">

            <!-- ===============================
                 LISTA DE PRODUCTOS
            =============================== -->
            <div class="col-md-8">

                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-carrito">

                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($_SESSION['carrito'] as $item): ?>
                                    <tr>

                                        <!-- Nombre -->
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                        </td>

                                        <!-- Precio -->
                                        <td><?php echo number_format($item['precio'], 2); ?> €</td>

                                        <!-- Cantidad editable -->
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="actualizar_id" value="<?php echo $item['id']; ?>">

                                                <input type="number"
                                                       name="nueva_cantidad"
                                                       value="<?php echo $item['cantidad']; ?>"
                                                       min="1"
                                                       class="form-control form-control-sm input-cantidad"
                                                       onchange="this.form.submit()">

                                                <input type="hidden" name="actualizar" value="1">
                                            </form>
                                        </td>

                                        <!-- Subtotal -->
                                        <td>
                                            <strong>
                                                <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €
                                            </strong>
                                        </td>

                                        <!-- Botón eliminar -->
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="eliminar_id" value="<?php echo $item['id']; ?>">

                                                <button type="submit" name="eliminar" class="btn-eliminar"
                                                        onclick="return confirm('¿Eliminar este producto?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>

                        <!-- Botón vaciar carrito -->
                        <form method="POST" class="text-right">
                            <button type="submit" name="vaciar" class="btn btn-outline-danger"
                                    onclick="return confirm('¿Vaciar todo el carrito?')">
                                <i class="fas fa-trash-alt"></i> Vaciar Carrito
                            </button>
                        </form>

                    </div>
                </div>

            </div>

            <!-- ===============================
                 RESUMEN DEL PEDIDO
            =============================== -->
            <div class="col-md-4">

                <div class="resumen-pedido sticky-top">

                    <h4 class="mb-4">Resumen del Pedido</h4>

                    <!-- Totales -->
                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>€<?php echo number_format($total, 2); ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span><?php echo $total >= 15 ? 'GRATIS' : '€1.50'; ?></span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong>
                                €<?php echo number_format($total + ($total >= 15 ? 0 : 1.50), 2); ?>
                            </strong>
                        </div>

                    </div>

                    <!-- Tiempo estimado -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-clock"></i>
                        <strong>Tiempo estimado:</strong> <?php echo $tiempo_total + 15; ?> minutos
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <h5>Dirección de envío:</h5>
                        <p><?php echo htmlspecialchars($_SESSION['direccion']); ?></p>
                        <a href="perfil.php" class="btn btn-sm btn-outline-primary">Cambiar dirección</a>
                    </div>

                    <!-- Formulario de confirmación -->
                    <form action="procesar_pedido.php" method="POST">

                        <div class="form-group">
                            <label for="tipo">Tipo de pedido:</label>

                            <select name="tipo" id="tipo" class="form-control espacio" required>
                                <option value="domicilio">A domicilio</option>
                                <option value="recogida">Recogida en local</option>
                            </select>
                        </div>

                        <br>

                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-check-circle"></i> Confirmar Pedido
                        </button>

                    </form>

                </div>

            </div>

        </div>

        <?php endif; ?>

    </div>
    
    <!-- Scripts -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>

<?php $mysqli->close(); ?> <!-- Cerrar conexión -->
