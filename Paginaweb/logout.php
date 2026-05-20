<?php

session_start();
include("conexion.php");

/** @var mysqli $conn */

if (isset($_SESSION['usuario'])) {

    $usuario = $_SESSION['usuario'];
    $ip = $_SERVER['REMOTE_ADDR'];

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
        ip
    )
    VALUES
    (
        ?,
        ?,
        'LOGOUT',
        'usuarios',
        'Cierre de sesión del sistema',
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