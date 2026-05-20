<?php

$hosts = [
    "192.168.0.194", // nodo1
    "192.168.0.198"  // nodo2
];

$user = "operador3";
$pass = "!Oerador#3%";
$db = "SGR";

$conn = null;

foreach ($hosts as $host) {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        continue; // Intenta el siguiente host
    } else {
        break; // Conexión 
    }
}
if ($conn === null || $conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

?>