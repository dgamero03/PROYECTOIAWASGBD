<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

$mensaje = ''; // Mensaje de éxito o error

// ===============================
// SI SE ENVÍA EL FORMULARIO (POST)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tiempo = $_POST['tiempo_preparacion'];
    $categoria = $_POST['categoria'];
    $activo = 1; // Todos los productos nuevos están activos

    // ===============================
    // PROCESAR IMAGEN SUBIDA
    // ===============================
    $imagen = '';

    // Si el usuario subió una imagen
    if (!empty($_FILES['imagen']['name'])) {

        // Nombre del archivo
        $nombreImagen = basename($_FILES['imagen']['name']);

        // Ruta donde se guardará
        $rutaDestino = 'imagenes/' . $nombreImagen;

        // Mover archivo desde la carpeta temporal
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagen = $rutaDestino; // Guardar ruta en BD
        }
    }

    // ===============================
    // INSERTAR PRODUCTO EN LA BASE DE DATOS
    // ===============================
    $stmt = $mysqli->prepare("
        INSERT INTO productos 
        (nombre, descripcion, precio, tiempo_preparacion, categoria, imagen, activo) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // Enlazar parámetros
    $stmt->bind_param("ssdisss", $nombre, $descripcion, $precio, $tiempo, $categoria, $imagen, $activo);

    // Ejecutar y comprobar resultado
    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success text-center p-3 mb-4' 
                        style='font-size:18px; font-weight:bold; box-shadow: 0 0 10px rgba(0,128,0,0.3); border-radius:10px;'>
                        ✅ Producto añadido correctamente
                    </div>";
    } else {
        $mensaje = "<div class='alert alert-danger text-center'>Error al añadir el producto</div>";
    }

    $stmt->close(); // Cerrar consulta preparada
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

    <title>Añadir Producto</title>
</head>

<body class="body-anadir-productos">

<div class="anadir-card">

    <!-- Cabecera del formulario -->
    <div class="logo-anadir mb-4">
        <i class="fas fa-hamburger"></i>
        <h3>Añadir Producto</h3>
        <h4 class="text-muted">Cheese Burger</h4>
    </div>

    <!-- Mensaje de éxito o error -->
    <?php echo $mensaje; ?>
    <br>

    <!-- ===============================
         FORMULARIO DE AÑADIR PRODUCTO
    =============================== -->
    <form method="POST" enctype="multipart/form-data">

        <!-- Nombre -->
        <div class="form-group-anadir d-flex align-items-center mb-3">
            <label for="nombre" class="form-label campo-label-anadir">
                <i class="fas fa-tag"></i> Nombre del producto
            </label>
            <input type="text" name="nombre" id="nombre" class="form-control-anadir" required>
        </div>
        <br>

        <!-- Descripción -->
        <div class="form-group-anadir d-flex align-items-center mb-3">
            <label for="descripcion" class="form-label campo-label-anadir">
                <i class="fas fa-carrot"></i> Ingredientes
            </label>
            <textarea name="descripcion" id="descripcion" class="form-control-anadir" rows="2" required></textarea>
        </div>
        <br>

        <!-- Precio -->
        <div class="form-group-anadir d-flex align-items-center mb-3">
            <label for="precio" class="form-label campo-label-anadir">
                <i class="fas fa-euro-sign"></i> Precio 
            </label>
            <input type="number" step="0.01" name="precio" id="precio" class="form-control-anadir" required>
        </div>
        <br>

        <!-- Tiempo de preparación -->
        <div class="form-group-anadir d-flex align-items-center mb-3">
            <label for="tiempo_preparacion" class="form-label campo-label-anadir">
                <i class="fas fa-clock"></i> Tiempo (min)
            </label>
            <input type="number" name="tiempo_preparacion" id="tiempo_preparacion" class="form-control-anadir" required>
        </div>
        <br>

        <!-- Categoría -->
        <div class="form-group-anadir d-flex align-items-center mb-3">
            <label for="categoria" class="form-label campo-label-anadir">
                <i class="fas fa-bars"></i> Categoría
            </label>
            <select name="categoria" id="categoria" class="form-control-anadir" required>
                <option value="pizzas">Pizzas</option>
                <option value="bebidas">Bebidas</option>
                <option value="postres">Postres</option>
                <option value="entrantes">Entrantes</option>
                <option value="hamburguesas">Hamburguesas</option>
                <option value="baguettes">Baguettes</option>
            </select>
        </div>
        <br>

        <!-- Imagen -->
        <div class="form-group-anadir d-flex align-items-center mb-3">
            <label for="imagen" class="form-label campo-label-anadir">
                <i class="fas fa-image"></i> Imagen
            </label>
            <input type="file" name="imagen" id="imagen" class="form-control-anadir">
        </div>
        <br>

        <!-- Botones -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                <i class="fas fa-save"></i> Guardar producto
            </button>

            <button type="button" class="btn btn-primary btn-lg w-100 mb-2"
                    onclick="window.location.href='admin_panel.php'">
                <i class="fas fa-undo"></i> Volver al panel admin
            </button>    
        </div>

    </form>
</div>

</body>

<!-- ===============================
     SCRIPT PARA CAMPOS OBLIGATORIOS
=============================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const categoria = document.getElementById('categoria');
    const descripcion = document.getElementById('descripcion');
    const tiempo = document.getElementById('tiempo_preparacion');

    // Función que activa o desactiva "required"
    function actualizarRequired() {

        // Si es bebida, no necesita descripción ni tiempo
        if (categoria.value === 'bebidas') {
            descripcion.removeAttribute('required');
            tiempo.removeAttribute('required');

        // Para el resto, sí son obligatorios
        } else {
            descripcion.setAttribute('required', 'required');
            tiempo.setAttribute('required', 'required');
        }
    }

    actualizarRequired(); // Al cargar la página
    categoria.addEventListener('change', actualizarRequired); // Al cambiar categoría
});
</script>

</html>
