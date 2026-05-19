<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit;
}

include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Auditoría del Sistema</title>

    <!-- W3.CSS -->
    <link rel="stylesheet"
          href="https://www.w3schools.com/w3css/4/w3.css">

    <!-- JQuery para AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>

<body class="w3-light-grey">

    <!-- Barra superior -->
    <div class="w3-bar w3-blue">

        <span class="w3-bar-item">
            Sistema de Monitoreo y Auditoría
        </span>

        <a href="dashboard.php"
           class="w3-bar-item w3-button w3-right">
           Regresar
        </a>

    </div>

    <!-- Contenedor principal -->
    <div class="w3-container w3-margin-top">

        <!-- Tarjeta -->
        <div class="w3-card w3-white w3-padding">

            <h2>
                Actividad del Sistema
            </h2>

            <p>
                Monitoreo en tiempo real de usuarios y operaciones
                realizadas dentro del entorno SGR.
            </p>

            <!-- Tabla -->
            <div class="w3-responsive">

                <table class="w3-table-all w3-hoverable">

                    <thead>
                        <tr class="w3-blue">

                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Tabla</th>
                            <th>Descripción</th>
                            <th>IP</th>
                            <th>Estado</th>
                            <th>Fecha</th>

                        </tr>
                    </thead>

                    <tbody id="tablaAuditoria">

                        <!-- AJAX cargará aquí -->

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- AJAX -->
    <script>

        function cargarAuditoria() {

            $("#tablaAuditoria").load("cargar_auditoria.php");

        }

        // Cargar inmediatamente
        cargarAuditoria();

        // Actualizar cada 5 segundos
        setInterval(cargarAuditoria, 5000);

    </script>

</body>
</html>
