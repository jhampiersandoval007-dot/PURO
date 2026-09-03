<?php

$host = "localhost";
$usuario = "TU_USUARIO_MYSQL";
$password = "TU_CONTRASENA_MYSQL";
$base_datos = "puro_db";

$conexion = new mysqli($host, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}