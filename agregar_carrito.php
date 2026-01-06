<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    
    // Verificar que el producto existe y está activo
    $sql_producto = "SELECT * FROM productos WHERE id = $producto_id AND activo = 1";
    $result_producto = $mysqli->query($sql_producto);
    
    if ($result_producto->num_rows > 0) {
        $producto = $result_producto->fetch_assoc();
        
        // Inicializar carrito en sesión si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Verificar si el producto ya está en el carrito
        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $producto_id) {
                $item['cantidad'] += 1;
                $encontrado = true;
                break;
            }
        }
        
        // Si no estaba, añadirlo
        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id' => $producto_id,
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => 1,
                'tiempo_preparacion' => $producto['tiempo_preparacion']
            ];
        }
        
        // Redirigir a carrito o mantener en misma página
        if (isset($_POST['redirigir']) && $_POST['redirigir'] == 'carrito') {
            header('Location: carrito.php');
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?agregado=1');
        }
        exit();
    } else {
        echo "Producto no encontrado o no disponible";
    }
} else {
    header('Location: index.php');
}

$mysqli->close();
?>