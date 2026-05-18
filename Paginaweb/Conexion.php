<?php
$host = "192.168.0.195";
$user = "operador3";
$pass = "!Oerador#3%";
$db = "SGR";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>