<?php
session_start();
include("conexion.php");

/** @var mysqli $conn */
if (!($conn instanceof mysqli)) {
    die("No hay conexión válida a la base de datos.");
}

if (
    file_exists('maintenance.lock') &&
    (!isset($_SESSION['user']) || $_SESSION['rol'] !== 'admin')
) {
    $_SESSION['redirect_after_maintenance'] = $_SERVER['REQUEST_URI'];
    include('maintenance.php');
    exit;
}

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: index.html");
    exit;
}

$username = trim($_POST['username']);
$password = hash('sha256', $_POST['password']);

$stmt = $conn->prepare("
SELECT username, rol
FROM usuarios
WHERE username = ? AND password = ?
");

$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {

    $user = $result->fetch_assoc();

    $_SESSION['user'] = $user['username'];
    $_SESSION['usuario'] = $user['username'];
    $_SESSION['rol'] = $user['rol'];

    $ip = $_SERVER['REMOTE_ADDR'];

$mariadbUserResult = $conn->query("SELECT CURRENT_USER() AS usuario_db");
$mariadbUser = $mariadbUserResult->fetch_assoc()['usuario_db'];

$auditoria = $conn->prepare("
INSERT INTO auditoria
(
    usuario,
    usuario_mariadb,
    accion,
    tabla_afectada,
    descripcion,
    ip
)
VALUES
(
    ?,
    ?,
    'LOGIN',
    'usuarios',
    'Inicio de sesión en el sistema',
    ?
)
");

$auditoria->bind_param(
    "sss",
    $username,
    $mariadbUser,
    $ip
);

$auditoria->execute();

    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login fallido</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="page">

        <div class="card">

            <h1>Acceso denegado</h1>

            <p class="message error">
                 Usuario o contraseña incorrectos.
            </p>

            <nav>
                <a class="button-link" href="index.html">
                    Volver a iniciar sesión
                </a>
            </nav>

        </div>

    </div>

</body>
</html>