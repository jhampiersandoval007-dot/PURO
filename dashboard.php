<?php
require_once "auth_check.php";
require_once "rate_limit.php";
require_once "conexion.php";

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
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Inter", "Segoe UI", sans-serif;
            background: #06140c;
            color: #e8f5e9;
            min-height: 100vh;
        }

        .contenido {
            margin-left: 110px;
            padding: 42px 48px;
            max-width: 1400px;
            transition: margin-left 0.35s ease;
        }

        .encabezado {
            margin-bottom: 32px;
        }

        .etiqueta-panel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            margin-bottom: 14px;
            border: 1px solid rgba(52, 199, 89, 0.3);
            border-radius: 100px;
            color: #a7f3d0;
            background: rgba(16, 42, 28, 0.55);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            backdrop-filter: blur(12px);
        }

        .etiqueta-panel::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34c759;
            box-shadow: 0 0 10px #34c759;
            animation: pulsoPunto 2s ease-in-out infinite;
        }

        .encabezado h1 {
            color: #ffffff;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }

        .encabezado p {
            color: #a5b8aa;
            font-size: 15px;
            font-weight: 400;
            letter-spacing: -0.01em;
        }

        .encabezado strong {
            color: #e8f5e9;
            font-weight: 600;
        }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .bento-card {
            position: relative;
            background: rgba(12, 28, 18, 0.72);
            backdrop-filter: blur(28px) saturate(160%);
            -webkit-backdrop-filter: blur(28px) saturate(160%);
            border: 1px solid rgba(165, 214, 167, 0.18);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.38), inset 0 1px 1px rgba(255, 255, 255, 0.12);
            transition: transform 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.28s ease, border-color 0.28s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .bento-card:hover {
            transform: translateY(-3px);
            border-color: rgba(165, 214, 167, 0.38);
            box-shadow: 0 28px 60px rgba(0, 0, 0, 0.52), 0 0 32px rgba(52, 199, 89, 0.12);
        }

        .bento-hero {
            grid-column: span 7;
            min-height: 220px;
            background: linear-gradient(135deg, rgba(16, 42, 28, 0.82), rgba(8, 24, 15, 0.72));
        }

        .bento-co2 {
            grid-column: span 5;
            min-height: 220px;
        }

        .bento-temp {
            grid-column: span 4;
            min-height: 180px;
        }

        .bento-hum {
            grid-column: span 4;
            min-height: 180px;
        }

        .bento-status {
            grid-column: span 4;
            min-height: 180px;
        }

        .bento-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .bento-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(46, 125, 50, 0.2);
            border: 1px solid rgba(102, 187, 106, 0.3);
            color: #a5d6a7;
        }

        .bento-badge.pulse::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #34c759;
            box-shadow: 0 0 10px #34c759;
            animation: pulsoPunto 2s ease-in-out infinite;
        }

        .bento-badge.badge-amber {
            background: rgba(255, 179, 0, 0.15);
            border-color: rgba(255, 179, 0, 0.35);
            color: #ffd54f;
        }

        .bento-badge.badge-cyan {
            background: rgba(0, 188, 212, 0.15);
            border-color: rgba(0, 188, 212, 0.35);
            color: #80deea;
        }

        .bento-badge.badge-green {
            background: rgba(52, 199, 89, 0.18);
            border-color: rgba(52, 199, 89, 0.4);
            color: #a7f3d0;
        }

        .bento-sensor-tag {
            font-size: 12px;
            color: #a5b8aa;
            letter-spacing: 0.02em;
            font-weight: 500;
        }

        .bento-label {
            font-size: 12px;
            color: #a5b8aa;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
            display: block;
        }

        .bento-value-huge {
            font-size: 44px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            line-height: 1.1;
            margin-bottom: 6px;
            font-feature-settings: "tnum";
        }

        .bento-value {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            line-height: 1.1;
            margin-bottom: 6px;
            font-feature-settings: "tnum";
        }

        .bento-value-compact {
            font-size: 21px;
            font-weight: 750;
            color: #ffffff;
            margin-bottom: 6px;
            letter-spacing: -0.01em;
            line-height: 1.25;
        }

        .bento-sublabel {
            font-size: 13px;
            color: #a5b8aa;
            display: block;
        }

        .bento-hero-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .bento-orb {
            width: 76px;
            height: 76px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(52, 199, 89, 0.22) 0%, rgba(12, 28, 18, 0) 70%);
            box-shadow: 0 0 35px rgba(52, 199, 89, 0.25);
            flex-shrink: 0;
        }

        .bento-orb-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .orb-track {
            fill: none;
            stroke: rgba(165, 214, 167, 0.15);
            stroke-width: 6;
        }

        .orb-fill {
            fill: none;
            stroke: #34c759;
            stroke-width: 6;
            stroke-dasharray: 264;
            stroke-dashoffset: 70;
            stroke-linecap: round;
            filter: drop-shadow(0 0 6px #34c759);
        }

        .orb-status {
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #a7f3d0;
        }

        .bento-bar-track {
            width: 100%;
            height: 6px;
            border-radius: 10px;
            background: rgba(165, 214, 167, 0.15);
            margin-top: 14px;
            overflow: hidden;
        }

        .bento-bar-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #66bb6a, #81c784);
            box-shadow: 0 0 10px rgba(102, 187, 106, 0.6);
        }

        .historia-puro {
            background: rgba(12, 28, 18, 0.72);
            backdrop-filter: blur(28px) saturate(160%);
            -webkit-backdrop-filter: blur(28px) saturate(160%);
            border: 1px solid rgba(165, 214, 167, 0.18);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.38), inset 0 1px 1px rgba(255, 255, 255, 0.12);
        }

        .historia-recorrido {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin: 24px 0;
        }

        .historia-etapa {
            background: rgba(16, 42, 28, 0.45);
            border: 1px solid rgba(165, 214, 167, 0.14);
            border-radius: 18px;
            padding: 22px;
            display: flex;
            gap: 16px;
            transition: transform 0.25s ease, border-color 0.25s ease;
        }

        .historia-etapa:hover {
            transform: translateY(-2px);
            border-color: rgba(165, 214, 167, 0.32);
        }

        .historia-aprendizajes {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 20px;
        }

        .historia-aprendizaje {
            background: rgba(16, 42, 28, 0.35);
            border: 1px solid rgba(165, 214, 167, 0.12);
            border-radius: 18px;
            padding: 22px;
            transition: transform 0.25s ease, border-color 0.25s ease;
        }

        .historia-aprendizaje:hover {
            transform: translateY(-2px);
            border-color: rgba(165, 214, 167, 0.32);
        }

        @keyframes pulsoPunto {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.15); box-shadow: 0 0 16px #34c759; }
        }

        @media (max-width: 1100px) {
            .bento-hero { grid-column: span 12; }
            .bento-co2 { grid-column: span 12; }
            .bento-temp { grid-column: span 6; }
            .bento-hum { grid-column: span 6; }
            .bento-status { grid-column: span 12; }
            .historia-recorrido, .historia-aprendizajes { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .contenido {
                margin-left: 80px;
                padding: 24px 16px;
            }
            .bento-grid {
                grid-template-columns: 1fr;
            }
            .bento-hero, .bento-co2, .bento-temp, .bento-hum, .bento-status {
                grid-column: span 1;
            }
        }
    </style>
    <link rel="stylesheet" href="panel.css?v=20261019-bento-ultra">

</head>

<body>

<?php $pagina_actual = 'dashboard'; require_once "sidebar.php"; ?>

<div class="contenido">

    <div class="encabezado">
        <span class="etiqueta-panel">Monitoreo en tiempo real</span>
        <h1>Panel de Monitoreo Ambiental</h1>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($nombre); ?></strong></p>
    </div>

    <section class="bento-grid" aria-label="Métricas del sistema PURO">

        <article class="bento-card bento-hero">
            <div class="bento-card-header">
                <span class="bento-badge pulse">Monitoreo en vivo</span>
                <span class="bento-sensor-tag">MQ-135 · Sensor General</span>
            </div>
            <div class="bento-hero-body">
                <div>
                    <span class="bento-label">Índice Calidad del Aire</span>
                    <div class="bento-value-huge"><?php echo htmlspecialchars($calidadAire); ?></div>
                    <span class="bento-sublabel">Lectura analógica en tiempo real del prototipo</span>
                </div>
                <div class="bento-hero-graphic" aria-hidden="true">
                    <div class="bento-orb">
                        <svg viewBox="0 0 100 100" class="bento-orb-svg">
                            <circle cx="50" cy="50" r="42" class="orb-track"></circle>
                            <circle cx="50" cy="50" r="42" class="orb-fill"></circle>
                        </svg>
                        <span class="orb-status">Activo</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="bento-card bento-co2">
            <div class="bento-card-header">
                <span class="bento-badge">MG811</span>
                <span class="bento-sensor-tag">Dióxido de Carbono</span>
            </div>
            <div>
                <span class="bento-label">Concentración CO₂</span>
                <div class="bento-value-huge"><?php echo htmlspecialchars($co2); ?></div>
                <span class="bento-sublabel">Lectura analógica · En calibración</span>
            </div>
            <div class="bento-bar-track" aria-hidden="true">
                <div class="bento-bar-fill" style="width: 52%;"></div>
            </div>
        </article>

        <article class="bento-card bento-temp">
            <div class="bento-card-header">
                <span class="bento-badge badge-amber">DHT22</span>
                <span class="bento-sensor-tag">Térmico</span>
            </div>
            <div>
                <span class="bento-label">Temperatura</span>
                <div class="bento-value"><?php echo htmlspecialchars($temperatura); ?></div>
                <span class="bento-sublabel">Condición térmica del entorno</span>
            </div>
        </article>

        <article class="bento-card bento-hum">
            <div class="bento-card-header">
                <span class="bento-badge badge-cyan">DHT22</span>
                <span class="bento-sensor-tag">Humedad</span>
            </div>
            <div>
                <span class="bento-label">Humedad Relativa</span>
                <div class="bento-value"><?php echo htmlspecialchars($humedad); ?></div>
                <span class="bento-sublabel">Humedad del biofiltro</span>
            </div>
        </article>

        <article class="bento-card bento-status">
            <div class="bento-card-header">
                <span class="bento-badge badge-green">Sistema Activo</span>
                <span class="bento-sensor-tag">Prototipo</span>
            </div>
            <div>
                <span class="bento-label">Biofiltro Principal</span>
                <div class="bento-value-compact">PURO · Inst. Tec. Comercio Alvarez Plata</div>
                <span class="bento-sublabel">Microalgas: Chlorella vulgaris</span>
            </div>
        </article>

    </section>

    <section class="historia-puro" aria-labelledby="historia-titulo">
        <div class="historia-intro">
            <span class="etiqueta-panel">La historia detrás del monitoreo</span>
            <h2 id="historia-titulo">Una ciudad más consciente empieza por observar</h2>
            <p>PURO es un proyecto académico que explora cómo unir microalgas, biofiltración y tecnología para acercar el monitoreo ambiental a la vida urbana. Cada lectura es una oportunidad para aprender más sobre el lugar donde vivimos.</p>
        </div>

        <div class="historia-recorrido" aria-label="Etapas del proyecto PURO">
            <article class="historia-etapa">
                <span class="historia-numero" aria-hidden="true">01</span>
                <div>
                    <h3>Una pregunta urbana</h3>
                    <p>¿Cómo podemos explorar soluciones ambientales desde nuestro propio entorno? PURO nace como una propuesta académica que conecta naturaleza, ciencia y tecnología.</p>
                </div>
            </article>
            <article class="historia-etapa">
                <span class="historia-numero" aria-hidden="true">02</span>
                <div>
                    <h3>De la idea al prototipo</h3>
                    <p>El prototipo combina un biofiltro con microalgas y sensores: DHT22 para temperatura y humedad, MQ-135 para calidad general del aire y MG811 para explorar lecturas de CO₂. Un ESP32 conectará el equipo con esta plataforma; la integración y calibración siguen en desarrollo.</p>
                </div>
            </article>
            <article class="historia-etapa">
                <span class="historia-numero" aria-hidden="true">03</span>
                <div>
                    <h3>Conocimiento para compartir</h3>
                    <p>Hacer visibles las mediciones invita a estudiantes y comunidad a conversar sobre su entorno y a evaluar futuras ideas con información local.</p>
                </div>
            </article>
        </div>

        <div class="historia-aprendizajes">
            <article class="historia-aprendizaje">
                <span class="historia-etiqueta">En evaluación</span>
                <h3>¿Cuánto puede durar?</h3>
                <p>No hay una vida útil única para todos los biofiltros. Depende del equipo y su mantenimiento; el cultivo de microalgas también necesita luz y condiciones adecuadas para crecer. PURO aún no ha completado pruebas de duración.</p>
            </article>
            <article class="historia-aprendizaje">
                <span class="historia-etiqueta">Falta medir</span>
                <h3>¿A cuántos árboles equivale?</h3>
                <p>Todavía no podemos dar una equivalencia real. Primero debemos medir cuánto CO₂ captura el prototipo durante un periodo definido. La captura de un árbol también cambia según su especie, edad y entorno.</p>
            </article>
            <article class="historia-aprendizaje">
                <span class="historia-etiqueta">Aporte social</span>
                <h3>¿Cómo puede ayudar?</h3>
                <p>PURO busca acercar el monitoreo ambiental a la comunidad, apoyar el aprendizaje y generar información local. Su efecto en la calidad del aire y la salud todavía debe comprobarse.</p>
            </article>
        </div>

        <details class="historia-nota">
            <summary>¿Por qué contamos la historia con datos pendientes?</summary>
            <p>Queremos que PURO sea útil y también transparente. Antes de hablar de años de vida, árboles equivalentes o beneficios para la salud, necesitamos completar la integración, calibrar los sensores y medir el funcionamiento del prototipo.</p>
        </details>
    </section>

</div>

</body>
</html>
