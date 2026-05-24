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


$totalTablas = 0;

if ($result) {

    $totalTablas = $result->num_rows;
}


$queryStorage = "
SELECT ROUND (SUM(data_length + index_length)/1024,2) AS TotalKB
FROM information_schema.tables
WHERE table_schema = 'SGR'
";

$storageResult = $conn->query($queryStorage);

$totalKB = 0;

if ($storageResult) {

    $filaStorage = $storageResult->fetch_assoc();
    $totalKB = $filaStorage['TotalKB'];
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Reporte de Tablas</title>

<link rel="stylesheet"
href="https://www.w3schools.com/w3css/4/w3.css">

<meta http-equiv="refresh" content="5">

</head>

<body class="w3-light-grey">

<div class="w3-container">

<h2>Reporte de Tablas SGR</h2>

<table class="w3-table-all w3-white">

 <a href="dashboard.php"
           class="w3-button w3-blue w3-margin-bottom">
            Regresar al Dashboard
        </a>

<div class="w3-row-padding
            w3-margin-bottom">

    <div class="w3-half">

        <div class="w3-card
                    w3-green
                    w3-padding">

            <h3>Total de tablas</h3>

            <p>
                <?php
                echo $totalTablas;
                ?>
            </p>

        </div>

    </div>

    <div class="w3-half">

        <div class="w3-card
                    w3-orange
                    w3-padding">

            <h3>Almacenamiento</h3>

            <p>
                <?php
                echo $totalKB;
                ?>
                KB
            </p>

        </div>

    </div>

</div>

<tr class="w3-blue">

<th>Tabla</th>
<th>Motor</th>
<th>Datos</th>
<th>Tamaño KB</th>
<th>Fecha Creación</th>
<th>Estado</th>

</tr>

<?php

if ($result && $result->num_rows > 0) {

    while($fila = $result->fetch_assoc()) {

        $estado = "NORMAL";
        $claseEstado = "w3-green";

        if ($fila['Datos'] == 1000) {
            $estado = "ALTO USO";
            $claseEstado = "w3-red";
        }

        echo "
        <tr>

            <td>{$fila['Tabla']}</td>
            <td>{$fila['Motor']}</td>
            <td>{$fila['Datos']}</td>
            <td>{$fila['TamañoKB']}</td>
            <td>{$fila['Creacion']}</td>
            <td><span class='w3-tag($claseEstado)'>{$estado}</span></td>

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

<br>

<div class="w3-panel w3-pale-blue w3-leftbar w3-border-blue">

    <p>
        <b>Observacion</b>
        La información presentada corresponde al monitoreo estructural de 
        la base de datos SGR.
    </p>

</div>

</div>

</body>

</html>
