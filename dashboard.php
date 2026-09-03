<?php
session_start();

require_once "rate_limit.php";
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION["nombre"];

/* =========================
   ÚLTIMA MEDICIÓN DE CO2
   ========================= */

$sqlCO2 = "SELECT m.valor, m.unidad
           FROM mediciones m
           INNER JOIN sensores s
               ON m.id_sensor = s.id_sensor
           INNER JOIN tipos_sensores ts
               ON s.id_tipo_sensor = ts.id_tipo_sensor
           WHERE ts.nombre = 'CO2'
           ORDER BY m.fecha_hora DESC
           LIMIT 1";

$resultadoCO2 = $conexion->query($sqlCO2);

$co2 = "Sin datos";

if ($resultadoCO2 && $resultadoCO2->num_rows > 0) {
    $datoCO2 = $resultadoCO2->fetch_assoc();
    $co2 = $datoCO2["valor"] . " " . $datoCO2["unidad"];
}


/* =========================
   ÚLTIMA TEMPERATURA
   ========================= */

$sqlTemperatura = "SELECT m.valor, m.unidad
                   FROM mediciones m
                   INNER JOIN sensores s
                       ON m.id_sensor = s.id_sensor
                   INNER JOIN tipos_sensores ts
                       ON s.id_tipo_sensor = ts.id_tipo_sensor
                   WHERE ts.nombre = 'Temperatura'
                   ORDER BY m.fecha_hora DESC
                   LIMIT 1";

$resultadoTemperatura = $conexion->query($sqlTemperatura);

$temperatura = "Sin datos";

if ($resultadoTemperatura && $resultadoTemperatura->num_rows > 0) {
    $datoTemperatura = $resultadoTemperatura->fetch_assoc();
    $temperatura = $datoTemperatura["valor"] . " " . $datoTemperatura["unidad"];
}


/* =========================
   ÚLTIMA MEDICIÓN DE HUMEDAD
   ========================= */

$sqlHumedad = "SELECT m.valor, m.unidad
               FROM mediciones m
               INNER JOIN sensores s
                   ON m.id_sensor = s.id_sensor
               INNER JOIN tipos_sensores ts
                   ON s.id_tipo_sensor = ts.id_tipo_sensor
               WHERE ts.nombre = 'Humedad'
               ORDER BY m.fecha_hora DESC
               LIMIT 1";

$resultadoHumedad = $conexion->query($sqlHumedad);

$humedad = "Sin datos";

if ($resultadoHumedad && $resultadoHumedad->num_rows > 0) {
    $datoHumedad = $resultadoHumedad->fetch_assoc();
    $humedad = $datoHumedad["valor"] . " " . $datoHumedad["unidad"];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - PURO</title>

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

        .sidebar {
            position: fixed;
            width: 230px;
            height: 100vh;
            background: #123c32;
            color: white;
            padding: 30px 20px;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 12px;
            color: #b8d5cd;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover,
        .sidebar .activo-menu {
            background: #1f5d4d;
        }

        .sidebar .cerrar-sesion {
            margin-top: 35px;
            background: #a83232;
            text-align: center;
        }

        .sidebar .cerrar-sesion:hover {
            background: #8b2929;
        }

        .contenido {
            margin-left: 230px;
            padding: 35px;
        }

        .encabezado {
            margin-bottom: 30px;
        }

        .encabezado h1 {
            color: #123c32;
            margin-bottom: 8px;
        }

        .tarjetas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .tarjeta h3 {
            color: #607d78;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .valor {
            font-size: 30px;
            font-weight: bold;
            color: #123c32;
        }

        .estado {
            margin-top: 25px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .activo {
            color: #16824b;
            font-weight: bold;
        }

        @media (max-width: 800px) {
            .tarjetas {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="panel.css">

</head>

<body>

<div class="sidebar">

    <div class="logo">PURO</div>
    <div class="subtitle">Biofiltración Urbana</div>

    <a href="dashboard.php" class="activo-menu">Dashboard</a>
    <a href="biofiltros.php">Biofiltros</a>
    <a href="sensores.php">Sensores</a>
    <a href="mediciones.php">Mediciones</a>
    <a href="alertas.php">Alertas</a>
    <a href="reportes.php">Reportes</a>

    <a href="logout.php" class="cerrar-sesion">
        Cerrar sesión
    </a>

</div>


<div class="contenido">

    <div class="encabezado">
        <span class="etiqueta-panel">Monitoreo en tiempo real</span>
        <h1>Panel de Monitoreo Ambiental</h1>

        <p>
            Bienvenido,
            <strong><?php echo htmlspecialchars($nombre); ?></strong>
        </p>

    </div>


    <div class="tarjetas">

        <div class="tarjeta indicador-co2">
            <h3>CO₂</h3>

            <div class="valor">
                <?php echo htmlspecialchars($co2); ?>
            </div>
            <span class="detalle-tarjeta">Última lectura registrada</span>
        </div>


        <div class="tarjeta indicador-temperatura">
            <h3>Temperatura</h3>

            <div class="valor">
                <?php echo htmlspecialchars($temperatura); ?>
            </div>
            <span class="detalle-tarjeta">Condición térmica actual</span>
        </div>


        <div class="tarjeta indicador-humedad">
            <h3>Humedad</h3>

            <div class="valor">
                <?php echo htmlspecialchars($humedad); ?>
            </div>
            <span class="detalle-tarjeta">Humedad relativa registrada</span>
        </div>

    </div>


    <div class="estado">

        <h2>Estado del sistema</h2>

        <br>

        <p>
            Biofiltro principal:
            <strong>PURO · Inst. Tec. Comercio Alvarez Plata</strong>
        </p>

        <br>

        <p>
            Estado:
            <span class="activo">
                ● Activo
            </span>
        </p>

    </div>

</div>

</body>
</html>
