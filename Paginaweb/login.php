<?php

$host1 = "192.168.9.67"; // Nodo principal
$host2 = "192.168.9.44"; // Nodo secundario

$user = "jose_ivan";
$pass = "#GOAT1%";
$db = "SGR";

// Intentar conexión al nodo principal
$conn = new mysqli($host1, $user, $pass, $db);

// Si falla, intentar nodo secundario
if ($conn->connect_error) {

    $conn = new mysqli($host2, $user, $pass, $db);

    // Si también falla
    if ($conn->connect_error) {
        die("Error de conexión con ambos nodos.");
    }
}

?>
