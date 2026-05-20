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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Panel de control</h1>
            <p>Bienvenido: <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></p>
            <p class="status <?php echo $_SESSION['rol'] === 'admin' ? 'admin' : 'viewer'; ?>">Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
            <ul class="menu">
                <li><a href="ver_platillos.php">Ver Platillos</a></li>
                <li><a href="insert_platillo.php">Insertar Platillo</a></li>
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <li><a href="indexar_tablas.php">Indexar Tablas</a></li>
                    <li><a href="mantenimiento.php">Configurar Mantenimiento</a></li>
                    <li><a href="servidores.php">Monitoreo de Servidores</a></li>
                    <li><a href="auditoria.php">Audotoria de Usuarios</a></li>
                    <li><a href="reportes_db.php">Servicio de Tablas</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</body>
</html>