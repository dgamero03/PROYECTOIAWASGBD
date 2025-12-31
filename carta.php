<?php
session_start();
require 'conexion.php';

// Obtener productos por categoría
$categorias = ['pizzas', 'bebidas', 'postres', 'entrantes'];
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <title>Carta Completa - Cheese Burger</title>
</head>
<body>

<header class="cabecera-principal">

  <a href="carrito.php" class="icono-circulo icono-carrito">
      <i class="fas fa-shopping-cart"></i>
  </a>

  <div class="info-local">
      <p class="mb-0"><strong>📍 C. San Sebastián, 16, 11650</strong></p>
      <p class="mb-0"><strong>📞 956 731 391</strong></p>
  </div>

  <div class="contenedor-logo">
      <div class="logo-rombo">
          <a href="index.php"> 
              <img src="imagenes/logo.png" alt="Logo Cheese Burger">
          </a>
      </div>
  </div>

</header>

<nav class="navbar-principal carta-menu">
  <div class="container-fluid d-flex justify-content-center align-items-center">

    <a href="#bebidas">Bebidas</a>
    <a href="#entrantes">Entrantes</a>
    <a href="#pizzas">Pizzas</a>
    <a href="#hamburguesas">Hamburguesas</a>
    <a href="#baguettes">Baguettes</a>
    <a href="#postres">Postres</a>

  </div>
</nav>

<div class="container mt-4 mb-5">
    <h1 class="text-center mb-4">📋 Carta Completa</h1>
    
    <?php foreach ($categorias as $categoria): ?>
    <div class="row">
        <div class="col-12">
            <h2 id="<?php echo $categoria; ?>" class="categoria-titulo">
                <?php 
                $iconos = [
                    'pizzas' => 'fas fa-pizza-slice',
                    'bebidas' => 'fas fa-glass-martini',
                    'postres' => 'fas fa-ice-cream',
                    'entrantes' => 'fas fa-bread-slice'
                ];
                ?>
                <i class="<?php echo $iconos[$categoria] ?? 'fas fa-utensils'; ?>"></i>
                <?php echo ucfirst($categoria); ?>
            </h2>
        </div>
    </div>
    
    <div class="row">
        <?php
        $sql = "SELECT * FROM productos WHERE categoria = '$categoria' AND activo = 1 ORDER BY nombre";
        $resultado = $mysqli->query($sql);
        
        if ($resultado->num_rows > 0) {
            while($producto = $resultado->fetch_assoc()) {
        ?>
        <div class="col-md-3 mb-4">
            <div class="card card-producto">
                <?php if ($producto['imagen']): ?>
                    <img src="<?php echo $producto['imagen']; ?>" class="img-producto" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <?php else: ?>
                    <div class="img-producto bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-utensils fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                    
                    <p class="card-text small text-muted">
                        <?php echo htmlspecialchars($producto['description']); ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge badge-info">
                                <?php echo $producto['tiempo_preparacion']; ?> min
                            </span>
                            <h5 class="text-success mt-2 mb-0">
                                €<?php echo number_format($producto['precio'], 2); ?>
                            </h5>
                        </div>
                        
                        <form action="agregar_carrito.php" method="post">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" class="btn btn-naranja btn-sm">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo '<div class="col-12"><p class="text-center text-muted">No hay productos en esta categoría</p></div>';
        }
        ?>
    </div>
    <?php endforeach; ?>

    <!-- Secciones adicionales -->
    <h2 id="hamburguesas" class="categoria-titulo"><i class="fas fa-hamburger"></i> Hamburguesas</h2>
    <p class="text-center text-muted">No hay productos en esta categoría</p>

    <h2 id="baguettes" class="categoria-titulo"><i class="fas fa-bread-slice"></i> Baguettes</h2>
    <p class="text-center text-muted">No hay productos en esta categoría</p>
    
    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>

<?php $mysqli->close(); ?>
