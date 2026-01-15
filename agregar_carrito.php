<?php
session_start(); // Inicia la sesión
require 'conexion.php'; // Conexión a la base de datos

// ===============================
// PROCESAR PETICIÓN POST
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $producto_id = $_POST['producto_id']; // ID del producto enviado

    // ===============================
    // VERIFICAR QUE EL PRODUCTO EXISTE Y ESTÁ ACTIVO
    // ===============================
    $sql_producto = "SELECT * FROM productos WHERE id = $producto_id AND activo = 1";
    $result_producto = $mysqli->query($sql_producto);

    // Si el producto existe
    if ($result_producto->num_rows > 0) {

        $producto = $result_producto->fetch_assoc(); // Datos del producto

        // ===============================
        // INICIALIZAR CARRITO SI NO EXISTE
        // ===============================
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // ===============================
        // VERIFICAR SI EL PRODUCTO YA ESTÁ EN EL CARRITO
        // ===============================
        $encontrado = false;

        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $producto_id) {
                $item['cantidad'] += 1; // Aumentar cantidad
                $encontrado = true;
                break;
            }
        }

        unset($item); // Buenas prácticas al usar referencias

        // ===============================
        // SI NO ESTABA EN EL CARRITO, AÑADIRLO
        // ===============================
        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id' => $producto_id,
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => 1,
                'tiempo_preparacion' => $producto['tiempo_preparacion']
            ];
        }

        // ===============================
        // REDIRECCIÓN SEGÚN ORIGEN
        // ===============================
        if (isset($_POST['redirigir']) && $_POST['redirigir'] == 'carrito') {
            header('Location: carrito.php'); // Ir al carrito
        } else {
            // Volver a la página anterior con indicador de éxito
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?agregado=1');
        }

        exit();

    } else {
        // Producto no válido
        echo "Producto no encontrado o no disponible";
    }

} else {
    // Si no es POST, redirigir al inicio
    header('Location: index.php');
}

$mysqli->close(); // Cerrar conexión
?>
