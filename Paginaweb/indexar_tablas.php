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

if ($_SESSION['rol'] !== 'admin') {
    die("No autorizado. Solo administradores pueden indexar las tablas.");
}

if (!isset($_SESSION['rollback_backups'])) {
    $_SESSION['rollback_backups'] = [];
}

$message = '';
$type = '';
$queryTime = 0.0;

function safeTableName(string $value): bool {
    return preg_match('/^[A-Za-z0-9_]+$/', $value) === 1;
}

mysqli_report(MYSQLI_REPORT_OFF);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['table_name'], $_POST['action'])) {
        $table = $_POST['table_name'];
        $action = $_POST['action'];

        if (!safeTableName($table)) {
            $message = "Nombre de tabla no válido.";
            $type = "error";
        } else {
            $checkQuery = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE() AND TABLE_NAME = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param("s", $table);
            $stmt->execute();
            $checkResult = $stmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Get primary key or first column for indexing
                $columnQuery = "SELECT COLUMN_NAME FROM information_schema.columns 
                                WHERE table_schema = DATABASE() AND table_name = ? 
                                ORDER BY ORDINAL_POSITION LIMIT 1";
                $colStmt = $conn->prepare($columnQuery);
                $colStmt->bind_param("s", $table);
                $colStmt->execute();
                $colResult = $colStmt->get_result();
                $column = $colResult->fetch_assoc()['COLUMN_NAME'] ?? null;

                if (!$column) {
                    $message = "No se pudo determinar columna para indexar en '{$table}'.";
                    $type = "error";
                } else {
                    $indexName = "idx_{$table}_{$column}";
                    $backupName = "backup_{$table}_" . time();
                    $backupEnabled = false;

                    if ($action === 'create') {
                        $backupCreate = "CREATE TABLE `{$backupName}` LIKE `{$table}`";
                        $backupCopy = "INSERT INTO `{$backupName}` SELECT * FROM `{$table}`";

                        if ($conn->query($backupCreate) && $conn->query($backupCopy)) {
                            $backupEnabled = true;
                        }

                        $start = microtime(true);
                        $createIndexQuery = "ALTER TABLE `{$table}` ADD INDEX `{$indexName}` (`{$column}`)";
                        if ($conn->query($createIndexQuery)) {
                            $duration = round((microtime(true) - $start) * 1000, 2);
                            if ($backupEnabled) {
                                $message = "Índice '{$indexName}' creado en '{$table}' en {$duration} ms. Backup creado para rollback.";
                                $_SESSION['rollback_backups'][$table] = $backupName;
                            } else {
                                $message = "Índice '{$indexName}' creado en '{$table}' en {$duration} ms. Rollback no disponible.";
                            }
                            $type = "success";
                        } else {
                            $message = "Error al crear índice en '{$table}': " . $conn->error;
                            $type = "error";
                            if ($backupEnabled) {
                                $conn->query("DROP TABLE IF EXISTS `{$backupName}`");
                            }
                        }
                    } elseif ($action === 'drop') {
                        $backupCreate = "CREATE TABLE `{$backupName}` LIKE `{$table}`";
                        $backupCopy = "INSERT INTO `{$backupName}` SELECT * FROM `{$table}`";

                        if ($conn->query($backupCreate) && $conn->query($backupCopy)) {
                            $backupEnabled = true;
                        }

                        $start = microtime(true);
                        $dropIndexQuery = "ALTER TABLE `{$table}` DROP INDEX `{$indexName}`";
                        if ($conn->query($dropIndexQuery)) {
                            $duration = round((microtime(true) - $start) * 1000, 2);
                            if ($backupEnabled) {
                                $message = "Índice '{$indexName}' eliminado de '{$table}' en {$duration} ms. Backup creado para rollback.";
                                $_SESSION['rollback_backups'][$table] = $backupName;
                            } else {
                                $message = "Índice '{$indexName}' eliminado de '{$table}' en {$duration} ms. Rollback no disponible.";
                            }
                            $type = "success";
                        } else {
                            $message = "Error al eliminar índice de '{$table}': " . $conn->error;
                            $type = "error";
                            if ($backupEnabled) {
                                $conn->query("DROP TABLE IF EXISTS `{$backupName}`");
                            }
                        }
                    } else {
                        $message = "Acción no válida.";
                        $type = "error";
                    }
                }
            } else {
                $message = "Tabla no encontrada.";
                $type = "error";
            }
        }
    } elseif (isset($_POST['rollback_table'], $_POST['backup_name'])) {
        $table = $_POST['rollback_table'];
        $backupName = $_POST['backup_name'];

        if (!safeTableName($table) || !safeTableName($backupName)) {
            $message = "Datos de rollback no válidos.";
            $type = "error";
        } elseif (!isset($_SESSION['rollback_backups'][$table]) || $_SESSION['rollback_backups'][$table] !== $backupName) {
            $message = "No hay rollback disponible para esta tabla.";
            $type = "error";
        } else {
            $restoreName = "{$table}_restore_" . time();
            $rollbackSql = "RENAME TABLE `{$table}` TO `{$restoreName}`, `{$backupName}` TO `{$table}`";

            $start = microtime(true);
            if ($conn->query($rollbackSql)) {
                $conn->query("DROP TABLE `{$restoreName}`");
                $duration = round((microtime(true) - $start) * 1000, 2);
                $message = "Rollback completado para '{$table}' en {$duration} ms.";
                $type = "success";
                unset($_SESSION['rollback_backups'][$table]);
            } else {
                $message = "Error al restaurar tabla '{$table}': " . $conn->error;
                $type = "error";
            }
        }
    }
}

$query = "SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH
          FROM information_schema.tables
          WHERE table_schema = DATABASE()
          ORDER BY TABLE_NAME";
$start = microtime(true);
$result = $conn->query($query);
$queryTime = round((microtime(true) - $start) * 1000, 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indexar Tablas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Indexar Tablas</h1>
            <p>Listado de tablas de la base de datos <strong><?php echo htmlspecialchars($conn->query('SELECT DATABASE()')->fetch_row()[0]); ?></strong>.</p>
            <p class="note">Consulta de tablas completada en <?php echo htmlspecialchars($queryTime); ?> ms.</p>
            <?php if ($message): ?>
                <p class="message <?php echo $type; ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tabla</th>
                            <th>Filas</th>
                            <th>Tamaño (bytes)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php $tableName = $row['TABLE_NAME']; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tableName); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['TABLE_ROWS'] ?? 0)); ?></td>
                                    <td><?php echo htmlspecialchars(number_format(($row['DATA_LENGTH'] ?? 0) + ($row['INDEX_LENGTH'] ?? 0))); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($tableName); ?>">
                                            <input type="hidden" name="action" value="create">
                                            <button type="submit">Crear Índice</button>
                                        </form>
                                        <form method="POST" style="display: inline; margin-left: 8px;">
                                            <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($tableName); ?>">
                                            <input type="hidden" name="action" value="drop">
                                            <button type="submit">Eliminar Índice</button>
                                        </form>
                                        <?php if (isset($_SESSION['rollback_backups'][$tableName])): ?>
                                            <form method="POST" style="display: inline; margin-left: 8px;">
                                                <input type="hidden" name="rollback_table" value="<?php echo htmlspecialchars($tableName); ?>">
                                                <input type="hidden" name="backup_name" value="<?php echo htmlspecialchars($_SESSION['rollback_backups'][$tableName]); ?>">
                                                <button type="submit">Rollback</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No se encontraron tablas en la base de datos.</td>
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

