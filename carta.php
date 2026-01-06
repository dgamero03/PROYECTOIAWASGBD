<?php
session_start();
require 'conexion.php';

// Obtener productos por categoría
$categorias = ['bebidas', 'entrantes', 'baguettes', 'hamburguesas', 'pizzas', 'postres'];
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
    <title>Carta Completa - Cheese Burger</title>
</head>
<body>

<header class="cabecera-principal">

  <a href="carrito.php" class="icono-circulo icono-carrito">
      <i class="fas fa-shopping-cart"></i>
  </a>

    <!-- Icono de información -->
    <a tabindex="0" class="icono-circulo icono-info" role="button"
   data-bs-toggle="popover"
   data-bs-trigger="hover focus"
   title="Información del local"
   data-bs-content="📞 956 731 391<br>
📍 C. San Sebastián, 16, 11650<br>
<strong style='color:#800000;'>Cómo llegar</strong><br>
<a href='https://www.google.com/maps/place/Pizzeria+Cheese+Burguer/@36.8599038,-5.6507068,17z' 
   target='_blank' 
   style='color:#800000; font-weight:600; text-decoration:underline; display:block; margin-top:8px;'>
   Ver en Google Maps
</a>"
   data-bs-html="true">
   <i class="fas fa-info-circle"></i>
</a>

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
    <a href="#baguettes">Baguettes</a>
    <a href="#hamburguesas">Hamburguesas</a>
    <a href="#pizzas">Pizzas</a>
    <a href="#postres">Postres</a>

  </div>
</nav>

<div class="container contenedor-carta mt-2 mb-3">
    <div class="text-center mb-4">
  <h1 style="font-family: 'Georgia', serif; font-size: 2.8rem; color: #800000; position: relative; margin-top: 20px;">
    <i class="fas fa-clipboard-list me-2" style="color: #f6c453;"></i> Carta Completa
    <span style="display: block; height: 3px; width: 80px; background-color: #f6c453; margin: 10px auto 0;"></span>
  </h1>
</div>

    
    <?php foreach ($categorias as $categoria): ?>
    <div class="row">
        <div class="col-12">
            <h2 id="<?php echo $categoria; ?>" class="categoria-titulo">
                <?php 
                $iconos = [
                    'bebidas' => 'fas fa-glass-whiskey',
                    'entrantes' => 'fas fa-concierge-bell',
                    'baguettes' => 'fas fa-bread-slice',
                    'hamburguesas' => 'fas fa-hamburger',
                    'pizzas' => 'fas fa-pizza-slice',
                    'postres' => 'fas fa-ice-cream'
                ];
                ?>
                <i class="<?php echo $iconos[$categoria] ?? 'fas fa-utensils'; ?> icono-categoria-<?php echo $categoria; ?>"></i>
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
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card card-producto">
                <?php if ($producto['imagen']): ?>
                    <img src="<?php echo $producto['imagen']; ?>" 
                        class="img-producto <?php echo ($producto['categoria'] === 'bebidas') ? 'img-bebida' : ''; ?>" 
                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <?php else: ?>
                    <div class="img-producto bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-utensils fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                    
                    <?php if (!empty($producto['descripcion'])): ?>

                    <p class="card-text small text-muted">
                        <?php echo htmlspecialchars($producto['descripcion']); ?>
                    </p>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <?php if (!empty($producto['tiempo_preparacion'])): ?>
                                <span class="badge badge-info">
                                    <?php echo $producto['tiempo_preparacion']; ?> min
                                </span>
                            <?php endif; ?>

                            <h5 class="text-success mt-2 mb-0">
                                €<?php echo number_format($producto['precio'], 2); ?>
                            </h5>
                        </div>    

                        <form action="agregar_carrito.php" method="post">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-cart-plus"></i> Añadir al carrito
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
</div>

<script src="js/jquery-3.4.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });
});
</script>



</body>
</html>

<?php $mysqli->close(); ?>
