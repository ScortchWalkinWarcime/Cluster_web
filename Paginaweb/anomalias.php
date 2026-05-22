<?php

include("Conexion.php");

/** @var mysqli $conn */
if (!($conn instanceof mysqli)) {
    die("No hay conexión válida a la base de datos.");
}

$query = "
SELECT *
FROM auditoria
WHERE
(
    TIME(fecha) < '08:00:00'
    OR TIME(fecha) >= '14:15:00'
)
ORDER BY fecha DESC
";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang='es'>

<head>

<meta charset='UTF-8'>

<title>Anomalías</title>

<link rel='stylesheet'
href='https://www.w3schools.com/w3css/4/w3.css'>

</head>

<body class='w3-light-grey'>

<div class='w3-container'>

<h2 class='w3-text-red'>
Detección de anomalías
</h2>

<table class='w3-table-all w3-white'>

 <a href="dashboard.php"
           class="w3-button w3-red w3-margin-bottom">
            Regresar al Dashboard
        </a>

<tr class='w3-red'>

<th>ID</th>
<th>Usuario</th>
<th>Usuario MariaDB</th>
<th>Acción</th>
<th>Tabla</th>
<th>Descripción</th>
<th>IP</th>
<th>Hora</th>
<th>Estado</th>

</tr>

<?php

if ($result && $result->num_rows > 0) {

    while($fila = $result->fetch_assoc()) {

        $hora = date(
            'H:i:s',
            strtotime($fila['fecha'])
        );

        if (
            $hora < '08:00:00'
            || $hora >= '14:00:00'
        ) {

            $estado = "SOSPECHOSO";
            $color = "w3-red";

        } else {

            $estado = "NORMAL";
            $color = "w3-green";
        }

        echo "

        <tr>

            <td>{$fila['id']}</td>

            <td>{$fila['usuario']}</td>

            <td>{$fila['usuario_mariadb']}</td>

            <td>{$fila['accion']}</td>

            <td>{$fila['tabla_afectada']}</td>

            <td>{$fila['descripcion']}</td>

            <td>{$fila['ip']}</td>

            <td>$hora</td>

            <td>

                <span class='w3-tag $color'>
                    $estado
                </span>

            </td>

        </tr>

        ";
    }

} else {

    echo "

    <tr>

        <td colspan='9'
        class='w3-center'>

            No se detectaron anomalías.

        </td>

    </tr>

    ";
}

?>

</table>

</div>

</body>
</html>