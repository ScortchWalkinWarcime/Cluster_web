<?php

include("conexion.php");

$sql = "SELECT * FROM auditoria
        ORDER BY fecha DESC
        LIMIT 20";

$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {

    while ($fila = $resultado->fetch_assoc()) {

        // Color para estado
        $colorEstado = "w3-green";

        if ($fila['estado'] == "FUERA_HORARIO") {
            $colorEstado = "w3-red";
        }

        echo "

        <tr>

            <td>{$fila['id']}</td>

            <td>{$fila['usuario']}</td>

            <td>{$fila['accion']}</td>

            <td>{$fila['tabla_afectada']}</td>

            <td>{$fila['descripcion']}</td>

            <td>{$fila['ip']}</td>

            <td>
                <span class='w3-tag $colorEstado'>
                    {$fila['estado']}
                </span>
            </td>

            <td>{$fila['fecha']}</td>

        </tr>

        ";
    }

} else {

    echo "

    <tr>

        <td colspan='8'
            class='w3-center w3-text-gray'>

            No hay eventos registrados.

        </td>

    </tr>

    ";
}

?>
