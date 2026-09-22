<?php
    $servidor = "localhost";
    $usuario  = "resikqao_admin_app_bef";
    $clave    = "3@hFwEQbZ#DDH5qGQAD29D";
    $bd       = "resikqao_db_apv";

    $conexion = new mysqli($servidor, $usuario, $clave, $bd);

    if ($conexion->connect_error) {
        die("Error en la conexión: " . $conexion->connect_error);
    }

    if (!$conexion->set_charset("utf8")) {
        die("Error al configurar el juego de caracteres utf8: " . $conexion->error);
    }
?>