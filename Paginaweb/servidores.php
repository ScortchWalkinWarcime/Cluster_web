<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Monitoreo de Servidores</title>

    <link rel="stylesheet"
          href="https://www.w3schools.com/w3css/4/w3.css">

    <script>

        function cargarServidores() {

            fetch('cargar_servidores.php')

            .then(response => response.text())

            .then(data => {

                document.getElementById(
                    "contenidoServidores"
                ).innerHTML = data;

            });

        }

        setInterval(cargarServidores, 5000);

        window.onload = cargarServidores;

    </script>

</head>

<body class="w3-light-grey">

    <div class="w3-container w3-padding">

        <h2 class="w3-text-blue">
            Monitoreo de Servidores
        </h2>

        <a href="dashboard.php"
           class="w3-button w3-blue w3-margin-bottom">
            Regresar al Dashboard
        </a>

        <div id="contenidoServidores">

            <div class="w3-panel w3-white w3-card w3-padding">
                Cargando información...
            </div>

        </div>

    </div>

</body>
</html>
