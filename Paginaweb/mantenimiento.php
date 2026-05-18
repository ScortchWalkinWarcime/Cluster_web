<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit;
}

if ($_SESSION['rol'] !== 'admin') {
    die("No autorizado.");
}

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['activate'])) {
        if (!file_exists('maintenance.lock')) {
            file_put_contents('maintenance.lock', '1');
            $message = "Modo mantenimiento activado.";
            $type = "success";
        } else {
            $message = "El modo mantenimiento ya está activado.";
            $type = "error";
        }
    } elseif (isset($_POST['deactivate'])) {
        if (file_exists('maintenance.lock')) {
            unlink('maintenance.lock');
            $message = "Modo mantenimiento desactivado.";
            $type = "success";
        } else {
            $message = "El modo mantenimiento ya está desactivado.";
            $type = "error";
        }
    }
}

$isMaintenance = file_exists('maintenance.lock');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Mantenimiento</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Configuración de Mantenimiento</h1>
            <p>Estado actual: <strong><?php echo $isMaintenance ? 'Activado' : 'Desactivado'; ?></strong></p>
            <?php if ($message): ?>
                <p class="message <?php echo $type; ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form method="POST" style="display: inline;">
                <button type="submit" name="activate" <?php echo $isMaintenance ? 'disabled' : ''; ?>>Activar Mantenimiento</button>
            </form>
            <form method="POST" style="display: inline; margin-left: 8px;">
                <button type="submit" name="deactivate" <?php echo !$isMaintenance ? 'disabled' : ''; ?>>Desactivar Mantenimiento</button>
            </form>
            <nav style="margin-top: 16px;">
                <a href="dashboard.php">← Volver al dashboard</a>
            </nav>
        </div>
    </div>
</body>
</html>