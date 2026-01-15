<?php
session_start(); // Inicia la sesión para poder destruirla

session_destroy(); // Elimina TODA la sesión del usuario (cierra sesión)

header('Location: index.php'); // Redirige al inicio después de cerrar sesión
exit(); // Detiene la ejecución del script
?>
