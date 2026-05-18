<?php
session_start();
include("conexion.php");

if (file_exists('maintenance.lock') && (!isset($_SESSION['user']) || $_SESSION['rol'] !== 'admin')) {
    $_SESSION['redirect_after_maintenance'] = $_SERVER['REQUEST_URI'];
    include('maintenance.php');
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit;
}

$query = "SELECT p.id_platillo, p.nombre, p.precio, c.nombre AS categoria
          FROM Platillo p
          JOIN Categoria c ON p.id_categoria = c.id_categoria";

$start = microtime(true);
$result = $conn->query($query);
$queryTime = round((microtime(true) - $start) * 1000, 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Platillos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>📋 Lista de Platillos</h1>
            <p class="note">Consulta completada en <?php echo htmlspecialchars($queryTime); ?> ms.</p>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_platillo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($row['precio'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No hay platillos registrados o no se pudo obtener la información.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <nav>
                <a href="dashboard.php">← Volver al dashboard</a>
            </nav>
        </div>
    </div>
</body>
</html>