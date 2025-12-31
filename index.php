<?php
session_start();
require 'conexion.php';

// NOVEDADES
$sql_novedades = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC LIMIT 4";
$resultado_novedades = $mysqli->query($sql_novedades);

// MÁS VENDIDOS
$sql_vendidos = "
    SELECT p.*, IFNULL(SUM(d.cantidad), 0) AS total_vendido
    FROM productos p
    LEFT JOIN detalles_de_pedidos d ON p.id = d.producto_id
    WHERE p.activo = 1
    GROUP BY p.id
    ORDER BY total_vendido DESC
    LIMIT 4
";
$resultado_vendidos = $mysqli->query($sql_vendidos);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pizzería Cheese Burger 🍕</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos.css (el añadido de php es para que cargue siempre en el navegador, 
     ya que hay veces que no carga la nueva modificacion) -->
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
            <a href="anadir_productos.php"><i class="fas fa-plus-circle"></i> Añadir Productos </a>
        <?php } ?>

        <a href="logout.php">Cerrar sesión</a>
        
    <?php } else { ?>
        <a href="login.php">Iniciar sesión</a>
        <a href="registro.php">Registrarse</a>
    <?php } ?>

  </div>
</nav>

<div class="contenedor-principal text-center">

    <div class="bloque-titulo">
        <h1>Pizzería Cheese Burger 🍕</h1>
        <p>Las mejores pizzas y hamburguesas</p>
    </div>

    <h3 class="mb-4 text-danger">🆕 Novedades</h3>
    <div class="row justify-content-center">

        <?php
        if ($resultado_novedades && $resultado_novedades->num_rows > 0) {
            while ($producto = $resultado_novedades->fetch_assoc()) {
        ?>
        <div class="col-md-3 mb-4">
            <div class="producto">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">
                <h5 class="mt-2"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <span class="badge bg-secondary">Tamaño único</span>
                <p class="mt-2"><strong><?php echo number_format($producto['precio'], 2); ?> €</strong></p>

                <form action="agregar_carrito.php" method="post">
                    <input type="hidden" name="producto_id" value="<?php echo (int)$producto['id']; ?>">
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-cart-plus"></i> Añadir al carrito
                    </button>
                </form>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No hay productos disponibles.</p>";
        }
        ?>
    </div>

    <hr>

    <h3 class="mb-4 text-danger">🔥 Más pedidos</h3>
    <div class="row justify-content-center">

        <?php
        if ($resultado_vendidos && $resultado_vendidos->num_rows > 0) {
            while ($producto = $resultado_vendidos->fetch_assoc()) {
        ?>
        <div class="col-md-3 mb-4">
            <div class="producto">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">
                <h5 class="mt-2"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <span class="badge bg-warning text-dark">Más vendido</span>
                <p class="mt-2"><strong><?php echo number_format($producto['precio'], 2); ?> €</strong></p>

                <form action="agregar_carrito.php" method="post">
                    <input type="hidden" name="producto_id" value="<?php echo (int)$producto['id']; ?>">
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-cart-plus"></i> Añadir al carrito
                    </button>
                </form>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No hay productos disponibles.</p>";
        }
        ?>
    </div>

    <footer class="text-center">
        <p>Cheese Burger - Proyecto ASGBD - Curso 2025/2026</p>
        <p><small>PHP · MySQL · Bootstrap</small></p>
    </footer>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
