<?php
session_start();
include("conexion.php");

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Error de conexión a la base de datos.");
}

if (file_exists('maintenance.lock') && (!isset($_SESSION['user']) || $_SESSION['rol'] !== 'admin')) {
    $_SESSION['redirect_after_maintenance'] = $_SERVER['REQUEST_URI'];
    include('maintenance.php');
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit;
}

if ($_SESSION['rol'] === 'viewer') {
    die("No autorizado.");
}

if (!isset($_POST['nombre'], $_POST['precio'], $_POST['categoria'])) {
    header("Location: insert_platillo.php");
    exit;
}

$nombre = trim($_POST['nombre']);
$precio = trim($_POST['precio']);
$categoria = trim($_POST['categoria']);

$precioDecimal = floatval($precio);
$categoriaId = intval($categoria);

$stmt = $conn->prepare("INSERT INTO Platillo (nombre, precio, id_categoria) VALUES (?, ?, ?)");
if ($stmt === false) {
    die("Error de preparación: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("sdi", $nombre, $precioDecimal, $categoriaId);

$start = microtime(true);
if ($stmt->execute()) {
    $duration = round((microtime(true) - $start) * 1000, 2);
    $message = "Platillo agregado correctamente.";
    $type = "success";
} else {
    $duration = round((microtime(true) - $start) * 1000, 2);
    $message = "Error al guardar: " . $stmt->error;
    $type = "error";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardar Platillo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Guardar Platillo</h1>
            <p class="message <?php echo $type; ?>"><?php echo htmlspecialchars($message); ?></p>
            <p class="note">Operación de base de datos completada en <?php echo htmlspecialchars($duration); ?> ms.</p>
            <nav>
                <a href="insert_platillo.php">← Volver al formulario</a>
                <a href="dashboard.php">Volver al dashboard</a>
            </nav>
        </div>
    </div>
</body>
</html>