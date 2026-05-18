<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>🚧 Servidor en Mantenimiento</h1>
            <p class="message error">El servidor está fuera de servicio por mantenimiento o apagado. No se pueden realizar operaciones en este momento.</p>
            <p>Por favor, intenta de nuevo más tarde o cierra sesión.</p>
            <form action="logout.php" method="POST">
                <button type="submit">Cerrar Sesión</button>
            </form>
            <form action="<?php echo htmlspecialchars($_SESSION['redirect_after_maintenance'] ?? 'dashboard.php'); ?>" method="GET" style="margin-top: 8px;">
                <button type="submit">Verificar Estado</button>
            </form>
        </div>
    </div>
</body>
</html>