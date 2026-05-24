<?php

session_start();
include("conexion.php");

/** @var mysqli $conn */
if (!($conn instanceof mysqli)) {
    die("No hay conexión válida a la base de datos.");
}

if (isset($_SESSION['usuario'])) {

    $usuario = $_SESSION['usuario'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $mariadbUserResult = $conn->query("
    SELECT CURRENT_USER() AS usuario_db
    ");

    $mariadbUser = $mariadbUserResult
    ->fetch_assoc()['usuario_db'];

    $auditoria = $conn->prepare("
    INSERT INTO auditoria
    (
        usuario,
        usuario_mariadb,
        accion,
        tabla_afectada,
        descripcion,
        ip,
        user_agent
    )
    VALUES
    (
        ?,
        ?,
        'LOGOUT',
        'usuarios',
        'Cierre de sesión del sistema',
        ?,
        ?
    )
    ");

    $auditoria->bind_param(
        "sss",
        $usuario,
        $mariadbUser,
        $ip
    );

    $auditoria->execute();
}

session_unset();
session_destroy();

header("Location: index.html");
exit;

?>
