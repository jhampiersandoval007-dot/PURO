<?php

echo "PHP FUNCIONA<br>";

$password = "";

var_dump($password);

$conexion = new mysqli("127.0.0.1", "root", "", "puro_db");

if ($conexion->connect_error) {
    die("ERROR: " . $conexion->connect_error);
}

echo "CONEXIÓN EXITOSA";