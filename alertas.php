<?php
require_once "auth_check.php";
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT
            a.id_alerta,
            a.tipo AS tipo_alerta,
            a.mensaje AS descripcion,
            a.nivel,
            a.fecha_hora,
            CASE ts.nombre WHEN 'CO2' THEN m.co2 WHEN 'Temperatura' THEN m.temperatura WHEN 'Humedad' THEN m.humedad WHEN 'Calidad del aire' THEN m.co2 END AS valor,
            CASE ts.nombre WHEN 'CO2' THEN 'ADC' WHEN 'Temperatura' THEN '°C' WHEN 'Humedad' THEN '%' WHEN 'Calidad del aire' THEN 'ADC' END AS unidad,
            ts.nombre AS tipo_sensor,
            b.nombre AS biofiltro
        FROM alertas a
        INNER JOIN mediciones m
            ON a.id_medicion = m.id_mediciones
        INNER JOIN sensores s
            ON m.id_sensor = s.id_sensor
        INNER JOIN tipos_sensores ts
            ON s.id_tipo_sensor = ts.id_tipo_sensor
        INNER JOIN biofiltros b
            ON s.id_biofiltro = b.id_biofiltro
        ORDER BY a.fecha_hora DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas - PURO</title>
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            color: #263238;
        }

        .encabezado {
            margin-bottom: 30px;
        }

        .encabezado h1 {
            color: #123c32;
            margin-bottom: 8px;
        }

        .tabla-contenedor {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #123c32;
            color: white;
            text-align: left;
            padding: 14px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e5e5;
        }

        tr:hover {
            background: #f4f7f6;
        }

        .nivel {
            font-weight: bold;
        }

        .nivel-Alto {
            color: #c62828;
        }

        .nivel-Medio {
            color: #e68a00;
        }

        .nivel-Bajo {
            color: #16824b;
        }

        .sin-datos {
            text-align: center;
            padding: 30px;
        }
    </style>
    <link rel="stylesheet" href="panel.css?v=20261019-glass-sidebar">
</head>

<body>

<?php $pagina_actual = 'alertas'; require_once "sidebar.php"; ?>

</body>
</html>
