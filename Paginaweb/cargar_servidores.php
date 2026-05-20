<?php

include("conexion.php");

/*OBTENER INFORMACIÓN GENERAL*/

$uptime = "Desconocido";
$threads = "Desconocido";
$queries = "Desconocido";

$status = $conn->query("SHOW STATUS");

while ($row = $status->fetch_assoc()) {

    if ($row['Variable_name'] == 'Uptime') {
        $uptime = $row['Value'];
    }

    if ($row['Variable_name'] == 'Threads_connected') {
        $threads = $row['Value'];
    }

    if ($row['Variable_name'] == 'Queries') {
        $queries = $row['Value'];
    }
}

/*INFORMACIÓN DEL HOST*/

$hostInfo = $conn->host_info;

/*VERIFICAR MASTER STATUS*/

$replicacion = "INACTIVA";

$master = $conn->query("SHOW MASTER STATUS");

if ($master && $master->num_rows > 0) {

    $replicacion = "ACTIVA";
}

/*MOSTRAR INFORMACIÓN*/

echo '

<div class="w3-row-padding">

    <div class="w3-quarter">

        <div class="w3-card w3-green w3-padding">

            <h3>Servidor</h3>

            <p>ONLINE</p>

        </div>

    </div>

    <div class="w3-quarter">

        <div class="w3-card w3-blue w3-padding">

            <h3>Replicación</h3>

            <p>'.$replicacion.'</p>

        </div>

    </div>

    <div class="w3-quarter">

        <div class="w3-card w3-orange w3-padding">

            <h3>Conexiones</h3>

            <p>'.$threads.'</p>

        </div>

    </div>

    <div class="w3-quarter">

        <div class="w3-card w3-red w3-padding">

            <h3>Consultas</h3>

            <p>'.$queries.'</p>

        </div>

    </div>

</div>

<br>

<div class="w3-card w3-white w3-padding">

    <h3>Información del servidor</h3>

    <p><b>Host:</b> '.$hostInfo.'</p>

    <p><b>Uptime:</b> '.$uptime.' segundos</p>

</div>

';

?>
