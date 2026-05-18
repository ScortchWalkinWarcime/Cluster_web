<?php
session_start();

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
    die("No tienes permisos para insertar.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Platillo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Insertar Platillo</h1>
            <form action="guardar_platillo.php" method="POST">
                <label>
                    Nombre
                    <input type="text" name="nombre" required>
                </label>
                <label>
                    Precio
                    <input type="text" name="precio" required>
                </label>
                <label>
                    ID de Categoría
                    <input type="text" name="categoria" required>
                </label>
                <button type="submit">Guardar</button>
            </form>
            <nav>
                <a href="dashboard.php">← Volver al dashboard</a>
            </nav>
        </div>
    </div>
</body>
</html>