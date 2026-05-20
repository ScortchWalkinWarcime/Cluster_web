<?php
require_once 'Conexion.php';
/** @var mysqli $conn */
if (!($conn instanceof mysqli)) {
    die("No hay conexión válida a la base de datos.");
}

$query = "
SELECT 
    table_name AS Tabla,
    engine AS Motor,
    table_rows AS Datos,
    ROUND((data_length + index_length)/1024,2) AS TamañoKB,
    create_time AS Creacion
FROM information_schema.tables
WHERE table_schema = 'SGR'
";

$result = $conn->query($query);


?>

<!DOCTYPE html>
<html>
<head>

<title>Reporte de Tablas</title>

<link rel="stylesheet"
href="https://www.w3schools.com/w3css/4/w3.css">

</head>

<body class="w3-light-grey">

<div class="w3-container">

<h2>Reporte de Tablas SGR</h2>

<table class="w3-table-all w3-white">

 <a href="dashboard.php"
           class="w3-button w3-blue w3-margin-bottom">
            Regresar al Dashboard
        </a>

<tr class="w3-blue">

<th>Tabla</th>
<th>Motor</th>
<th>Datos</th>
<th>Tamaño KB</th>
<th>Fecha Creación</th>

</tr>

<?php

if ($result && $result->num_rows > 0) {

    while($fila = $result->fetch_assoc()) {

        echo "
        <tr>

            <td>{$fila['Tabla']}</td>
            <td>{$fila['Motor']}</td>
            <td>{$fila['Datos']}</td>
            <td>{$fila['TamañoKB']}</td>
            <td>{$fila['Creacion']}</td>

        </tr>
        ";
    }

} else {

    echo "
    <tr>
        <td colspan='5'>
            No hay tablas registradas
        </td>
    </tr>
    ";
}

?>

</table>

</div>

</body>
</html>