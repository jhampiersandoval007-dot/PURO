<?php
session_start();
require_once "rate_limit.php";
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION["nombre"] ?? "Usuario";
$sqlCO2 = "SELECT m.co2
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
    $co2 = $datoCO2["co2"];
}

/* =========================
   ÚLTIMA MEDICIÓN DE CALIDAD DEL AIRE
   ========================= */

$sqlCalidadAire = "SELECT m.co2 AS valor
           FROM mediciones m
           INNER JOIN sensores s
               ON m.id_sensor = s.id_sensor
           INNER JOIN tipos_sensores ts
               ON s.id_tipo_sensor = ts.id_tipo_sensor
           WHERE ts.nombre = 'Calidad del aire'
           ORDER BY m.fecha_hora DESC
           LIMIT 1";

$resultadoCalidadAire = $conexion->query($sqlCalidadAire);

$calidadAire = "Sin datos";

if ($resultadoCalidadAire && $resultadoCalidadAire->num_rows > 0) {
    $datoCalidadAire = $resultadoCalidadAire->fetch_assoc();
    $calidadAire = $datoCalidadAire["valor"] . " ADC";
}


/* =========================
   ÚLTIMA TEMPERATURA
   ========================= */

$sqlTemperatura = "SELECT m.temperatura AS valor
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
    $temperatura = $datoTemperatura["valor"] . " °C";
}


/* =========================
   ÚLTIMA MEDICIÓN DE HUMEDAD
   ========================= */

$sqlHumedad = "SELECT m.humedad AS valor
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
    $humedad = $datoHumedad["valor"] . " %";
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
    <link rel="stylesheet" href="panel.css?v=20261008-historia">

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
    <?php if (empty($_SESSION["es_invitado"])): ?>
        <a href="reportes.php">Reportes</a>
    <?php endif; ?>

    <a href="logout.php" class="cerrar-sesion">
        <svg class="arr-1" viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7v-4H4v-2h9V5z"/></svg>
        <svg class="arr-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7v-4H4v-2h9V5z"/></svg>
        <span class="circle"></span>
        <span class="text">Cerrar sesión</span>
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
            <h3>CO₂ · MG811</h3>

            <div class="valor">
                <?php echo htmlspecialchars($co2); ?>
            </div>
            <span class="detalle-tarjeta">
                Lectura analógica pendiente de calibración
            </span>
        </div>

        <div class="tarjeta indicador-co2">
            <h3>Calidad del aire</h3>

            <div class="valor">
                <?php echo htmlspecialchars($calidadAire); ?>
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


    <section class="historia-puro" aria-labelledby="historia-titulo">
        <div class="historia-intro">
            <span class="etiqueta-panel">La historia detrás del monitoreo</span>
            <h2 id="historia-titulo">Una idea que quiere echar raíces en la ciudad</h2>
            <p>PURO une biofiltración urbana con microalgas y monitoreo ambiental. Es un prototipo en desarrollo para acercar la tecnología ambiental a las conversaciones sobre la ciudad que compartimos.</p>
        </div>

        <div class="historia-recorrido" aria-label="Etapas del proyecto PURO">
            <article class="historia-etapa">
                <span class="historia-numero" aria-hidden="true">01</span>
                <div>
                    <h3>Una pregunta urbana</h3>
                    <p>¿Cómo podemos explorar soluciones ambientales desde nuestro propio entorno? PURO nace como una propuesta académica que conecta naturaleza y tecnología.</p>
                </div>
            </article>
            <article class="historia-etapa">
                <span class="historia-numero" aria-hidden="true">02</span>
                <div>
                    <h3>De la idea al prototipo</h3>
                    <p>El sistema combina un biofiltro con microalgas y sensores para observar variables como CO₂, temperatura y humedad. La integración y calibración continúan en desarrollo.</p>
                </div>
            </article>
            <article class="historia-etapa">
                <span class="historia-numero" aria-hidden="true">03</span>
                <div>
                    <h3>Datos para conversar</h3>
                    <p>Hacer visibles las mediciones ayuda a entender mejor el entorno y abre la puerta a evaluar futuras mejoras con la comunidad.</p>
                </div>
            </article>
        </div>

        <details class="historia-nota">
            <summary>¿Qué significa que PURO esté en desarrollo?</summary>
            <p>El prototipo todavía requiere integrar y calibrar sus componentes. Por eso, las lecturas sirven para explorar el sistema; aún no representan una medición certificada de impacto ambiental ni una promesa de reducción de CO₂.</p>
        </details>
    </section>


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
