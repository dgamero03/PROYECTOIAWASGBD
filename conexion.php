<?php
    // Crear conexión a MySQL (host, usuario, contraseña, base de datos)
    $mysqli = new mysqli("localhost", "root", "", "pizzeria");

    // Comprobar si hubo error al conectar
    if($mysqli->connect_errno){
        echo "<p>Fallo al conectar a MySQL: (", $mysqli->connect_errno, ") ", $mysqli->connect_error, "</p>";
    } 
?>
