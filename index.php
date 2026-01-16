<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

/* ============================
   CONSULTA: NOVEDADES
   Últimos 4 productos activos
============================ */
$sql_novedades = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC LIMIT 4";
$resultado_novedades = $mysqli->query($sql_novedades);

/* ============================
   CONSULTA: MÁS VENDIDOS
   Productos con más unidades vendidas
============================ */
$sql_vendidos = "
    SELECT p.*, 
           COALESCE(SUM(d.cantidad), 0) AS total_vendido
    FROM productos p
    LEFT JOIN detalles_de_pedidos d ON p.id = d.producto_id
    WHERE p.activo = 1
    AND p.categoria != 'bebidas'
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

    <!-- Bootstrap 5 (necesario para el carrusel) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

<!-- ============================
     CABECERA PRINCIPAL
============================ -->
<header class="cabecera-principal">

  <!-- Icono menú -->
  <a href="carta.php" class="icono-circulo icono-menu">
    <img src="imagenes/logomenu.png" alt="Menu">
  </a>

  <!-- Icono carrito -->
  <a href="carrito.php" class="icono-circulo icono-carrito">
    <i class="fas fa-shopping-cart"></i>
  </a>

  <!-- Logo central -->
  <div class="contenedor-logo">
    <div class="logo-rombo">
      <img src="imagenes/logo.png" alt="Logo Cheese Burger">
    </div>
  </div>

</header>

<!-- ============================
     NAVBAR SUPERIOR
============================ -->
<nav class="navbar-principal">
  <div class="container-fluid d-flex justify-content-end align-items-center">

    <?php if (isset($_SESSION["nombre"])) { ?>
        <!-- Usuario logueado -->
        <span class="ms-3 me-3">Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>

        <?php if (!empty($_SESSION["administrador"]) && $_SESSION["administrador"] == 1) { ?>
            <span class="me-3">(Administrador)</span>
            <a href="admin_panel.php" style="color: #f6c453"> Panel Admin </a>
        <?php } ?>

        <a href="perfil.php">Perfil</a>
        <a href="mis_pedidos.php">Pedidos</a>
        <a href="logout.php">Cerrar sesión</a>
        
    <?php } else { ?>
        <!-- Usuario invitado -->
        <a href="login.php">Iniciar sesión</a>
        <a href="registro.php">Registrarse</a>
    <?php } ?>

  </div>
</nav>

<!-- ============================
     CONTENIDO PRINCIPAL
============================ -->
<div class="contenedor-principal text-center">

    <!-- Título principal -->
    <div class="bloque-titulo">
        <h1>Pizzería Cheese Burger</h1>
        <p>Las mejores pizzas y hamburguesas</p>
    </div>

    <!-- ============================
         SECCIÓN: NOVEDADES
         Últimos productos añadidos
    ============================ -->
    <h3 class="mb-4 titulo-color titulo-combinado">Novedades</h3>

    <div class="novedades">
        <div class="row justify-content-center">

            <?php
                if ($resultado_novedades && $resultado_novedades->num_rows > 0) {
                    $contador = 0;

                    while ($producto = $resultado_novedades->fetch_assoc()) {
                        $contador++;
            ?>

            <!-- 
                Control de visibilidad según tamaño de pantalla:
                - Los productos 3 y 4 se ocultan en móvil
                - El 4 se oculta en tablet pero aparece en pantallas grandes
            -->
            <div class="col-6 col-md-4 col-lg-3 mb-4 d-flex producto-novedad
                <?php if ($contador > 2) echo 'd-none d-md-flex'; ?>
                <?php if ($contador == 4) echo 'd-md-none d-lg-flex'; ?>
            ">

                <div class="producto">
                    <!-- Imagen -->
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">

                    <!-- Nombre -->
                    <h5 class="mt-2 mb-3"><?php echo htmlspecialchars($producto['nombre']); ?></h5>

                    <!-- Descripción -->
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>

                    <div class="mt-auto">
                        <!-- Precio -->
                        <div class="text-end mb-2">
                            <strong><?php echo number_format($producto['precio'], 2); ?> €</strong>
                        </div>

                        <!-- Botón añadir al carrito -->
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
    </div>

    <br>

    <!-- ============================
         SECCIÓN: TOP VENTAS
         Carrusel Bootstrap 5
    ============================ -->
    <h3 class="mb-4 titulo-color titulo-combinado">Top ventas</h3>

    <!-- 
        CARRUSEL BOOTSTRAP 5
        - data-bs-ride="carousel": activa el carrusel automáticamente
        - data-bs-interval="5000": cambia cada 5 segundos
    -->
    <div id="carouselVendidos" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-inner">

            <?php
            $active = true; // El primer item debe ser "active"

            while ($producto = $resultado_vendidos->fetch_assoc()) {
            ?>

            <!-- Cada producto es una diapositiva -->
            <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">

                <div class="d-flex justify-content-center">
                    <div class="col-10 col-sm-8 col-md-6 col-lg-3 d-flex">

                        <div class="producto w-100">

                            <!-- Imagen -->
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">

                            <!-- Nombre -->
                            <h5 class="mt-2 mb-2"><?php echo htmlspecialchars($producto['nombre']); ?></h5>

                            <!-- Descripción -->
                            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>

                            <div class="mt-auto">
                                <!-- Precio -->
                                <div class="text-end mb-2">
                                    <strong><?php echo number_format($producto['precio'], 2); ?> €</strong>
                                </div>

                                <!-- Botón añadir al carrito -->
                                <form action="agregar_carrito.php" method="post">
                                    <input type="hidden" name="producto_id" value="<?php echo (int)$producto['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-cart-plus"></i> Añadir al carrito
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <?php
                $active = false; // Solo el primero es active
            }
            ?>

        </div>

        <!-- Controles del carrusel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselVendidos" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselVendidos" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>
</div>

<!-- ============================
     FOOTER
============================ -->
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

<!-- ============================
     SCRIPTS BOOTSTRAP 5
     Necesarios para que funcione el carrusel
============================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
