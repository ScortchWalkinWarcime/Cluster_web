<?php

require_once 'Conexion.php';

/** @var mysqli $conn */
if (!($conn instanceof mysqli)) {
    die("No hay conexión válida a la base de datos.");
}

$query = "
SELECT *
FROM auditoria
ORDER BY fecha DESC
LIMIT 50
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {

    while ($fila = $result->fetch_assoc()) {

        echo "
        <tr>
            <td>{$fila['id']}</td>
            <td>{$fila['usuario']}</td>
            <td>{$fila['accion']}</td>
            <td>{$fila['tabla_afectada']}</td>
            <td>{$fila['descripcion']}</td>
            <td>{$fila['ip']}</td>
            <td>
                <span class='w3-tag w3-green'>
                    ACTIVO
                </span>
            </td>
            <td>{$fila['fecha']}</td>
        </tr>
        ";
    }

} else {

    echo "
    <tr>
        <td colspan='8'>
            No hay registros de auditoría.
        </td>
    </tr>
    ";
}

?>
