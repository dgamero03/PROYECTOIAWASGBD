<?php
session_start();
require 'conexion.php';

// NOVEDADES
$sql_novedades = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC LIMIT 4";
$resultado_novedades = $mysqli->query($sql_novedades);

// MÁS VENDIDOS
$sql_vendidos = "
    SELECT p.*, 
           COALESCE(SUM(d.cantidad), 0) AS total_vendido
    FROM productos p
    LEFT JOIN detalles_de_pedidos d ON p.id = d.producto_id
    WHERE p.activo = 1
    GROUP BY p.id
    HAVING total_vendido > 0
    ORDER BY total_vendido DESC
    LIMIT 4
";

$resultado_vendidos = $mysqli->query($sql_vendidos);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pizzería Cheese Burger</title>

    <!-- Bootstrap 5 (necesario para que el carrusel funcione) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

</head>

<body>

<header class="cabecera-principal">

  <a href="carta.php" class="icono-circulo icono-menu">
    <img src="imagenes/logomenu.png" alt="Menu">
  </a>

  <a href="carrito.php" class="icono-circulo icono-carrito">
  <i class="fas fa-shopping-cart"></i>
  </a>

  <div class="contenedor-logo">
    <div class="logo-rombo">
      <img src="imagenes/logo.png" alt="Logo Cheese Burger">
    </div>
  </div>

</header>

<nav class="navbar-principal">
  <div class="container-fluid d-flex justify-content-end align-items-center">

    <?php if (isset($_SESSION["nombre"])) { ?>
        <span class="ms-3 me-3">Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>

        <?php if (!empty($_SESSION["administrador"]) && $_SESSION["administrador"] == 1) { ?>
            <span class="me-3">(Administrador)</span>
            <a href="admin_panel.php" style= "color: #f6c453"> Panel Admin </a>
        <?php } ?>
        <a href="perfil.php">Perfil</a>
        <a href="mis_pedidos.php">Pedidos</a>
        <a href="logout.php">Cerrar sesión</a>
        
    <?php } else { ?>
        <a href="login.php">Iniciar sesión</a>
        <a href="registro.php">Registrarse</a>
    <?php } ?>

  </div>
</nav>

<div class="contenedor-principal text-center">

    <div class="bloque-titulo">
    <h1> Pizzería Cheese Burger</h1>
    <p>Las mejores pizzas y hamburguesas</p>
    </div>


    <h3 class="mb-4 text-danger">Novedades</h3>
    <div class="row justify-content-center">

        <?php
        if ($resultado_novedades && $resultado_novedades->num_rows > 0) {
            while ($producto = $resultado_novedades->fetch_assoc()) {
        ?>
        <div class="col-md-3 mb-4 d-flex">
            <div class="producto">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">
                <h5 class="mt-2 mb-4"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <span class="badge bg-secondary"></span>

                <div class="mt-auto">
                    <div class="text-end mb-2">
                        <strong><?php echo number_format($producto['precio'], 2); ?> €</strong>
                    </div>

                    <form action="agregar_carrito.php" method="post">
                        <input type="hidden" name="producto_id" value="<?php echo (int)$producto['id']; ?>">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-cart-plus"></i> Añadir al carrito
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No hay productos disponibles.</p>";
        }
        ?>
    </div>

    <br>

    <h3 class="mb-4 text-danger">Top ventas</h3>

    <!-- CARRUSEL -->
    <div id="carouselVendidos" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">

        <?php
        $contador = 0;
        while ($producto = $resultado_vendidos->fetch_assoc()) {
            if ($contador % 2 == 0) {
                echo '<div class="carousel-item ' . ($contador == 0 ? 'active' : '') . '"><div class="row justify-content-center">';
            }
        ?>
            <div class="col-md-4 mb-4 d-flex">
                <div class="producto">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">
                    <h5 class="mt-2 mb-2"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>

                    <div class="mt-auto">
                        <div class="text-end mb-2">
                            <strong><?php echo number_format($producto['precio'], 2); ?> €</strong>
                        </div>

                        <form action="agregar_carrito.php" method="post">
                            <input type="hidden" name="producto_id" value="<?php echo (int)$producto['id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-cart-plus"></i> Añadir al carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php
            $contador++;
            if ($contador % 2 == 0 || $contador == $resultado_vendidos->num_rows) {
                echo '</div></div>';
            }
        }
        ?>
        </div>

        <!-- Controles Bootstrap 5 -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselVendidos" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselVendidos" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <footer class="footer-cheese text-center">

    <h5 class="footer-titulo">Pizzería Cheese Burger</h5>

    <p class="footer-linea">
        <i class="fas fa-map-marker-alt"></i> C. San Sebastián, 16, 11650
    </p>

    <p class="footer-linea">
        <i class="fas fa-phone"></i> 956 731 391
    </p>

    <hr class="footer-separador">

    <p class="footer-texto">Cheese Burger - Proyecto IAWASGBD - Curso 2025/2026</p>
    <p class="footer-subtexto"><small>PHP · MySQL · Bootstrap</small></p>

    </footer>



</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
