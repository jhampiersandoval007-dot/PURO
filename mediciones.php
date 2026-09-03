<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

/* El sistema cuenta con un único prototipo: PURO Centro. */
$biofiltro_actual = "PURO Centro";

$sql = "SELECT
            ts.nombre AS tipo_sensor,
            DATE_FORMAT(m.fecha_hora, '%Y-%m') AS mes,
            AVG(m.valor) AS valor_promedio,
            m.unidad,
            COUNT(m.id_medicion) AS total_mediciones
        FROM mediciones m
        INNER JOIN sensores s ON m.id_sensor = s.id_sensor
        INNER JOIN tipos_sensores ts ON s.id_tipo_sensor = ts.id_tipo_sensor
        INNER JOIN biofiltros b ON s.id_biofiltro = b.id_biofiltro
        WHERE b.nombre = ?
        GROUP BY ts.nombre, DATE_FORMAT(m.fecha_hora, '%Y-%m'), m.unidad
        ORDER BY ts.nombre ASC, mes ASC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $biofiltro_actual);
$stmt->execute();
$resultado = $stmt->get_result();

$graficos = [];
$total_mediciones = 0;

while ($medicion = $resultado->fetch_assoc()) {
    $graficos[$medicion["tipo_sensor"]][] = $medicion;
    $total_mediciones += (int) $medicion["total_mediciones"];
}

$stmt->close();

function nombre_mes($fecha)
{
    $meses = [
        "01" => "Ene", "02" => "Feb", "03" => "Mar", "04" => "Abr",
        "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Ago",
        "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dic"
    ];

    $partes = explode("-", $fecha);
    return ($meses[$partes[1]] ?? $fecha) . " " . $partes[0];
}

function titulo_sensor($tipo_sensor)
{
    return $tipo_sensor === "CO2" ? "CO₂" : $tipo_sensor;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediciones - PURO</title>
    <link rel="stylesheet" href="panel.css">
    <style>
        .resumen-mediciones { display:flex; align-items:center; gap:12px; margin-top:18px; color:#a5b8aa; font-size:14px; }
        .indicador-activo { width:10px; height:10px; border-radius:50%; background:#66bb6a; box-shadow:0 0 14px rgba(102,187,106,.8); animation:pulsoMedicion 2s ease-in-out infinite; }
        .graficos-contenedor { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; }
        .grafico { min-height:410px; padding:24px; overflow:hidden; border:1px solid rgba(165,214,167,.15); border-radius:16px; background:rgba(12,28,18,.86); box-shadow:0 16px 40px rgba(0,0,0,.2); backdrop-filter:blur(12px); animation:entradaGrafico .55s ease both; }
        .grafico:nth-child(2) { animation-delay:.1s; } .grafico:nth-child(3) { animation-delay:.2s; }
        .grafico-cabecera { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; padding-bottom:18px; border-bottom:1px solid rgba(165,214,167,.1); }
        .grafico h2 { color:#e8f5e9; font-size:20px; } .grafico p { margin-top:6px; color:#a5b8aa; font-size:12px; }
        .unidad { padding:7px 10px; border:1px solid rgba(102,187,106,.22); border-radius:20px; color:#a5d6a7; background:rgba(76,175,80,.1); font-size:12px; font-weight:700; }
        .area-barras { display:flex; align-items:flex-end; gap:18px; height:270px; padding:28px 8px 0; border-bottom:1px solid rgba(165,214,167,.22); background-image:linear-gradient(to bottom,transparent 24%,rgba(165,214,167,.07) 25%,transparent 26%,transparent 49%,rgba(165,214,167,.07) 50%,transparent 51%,transparent 74%,rgba(165,214,167,.07) 75%,transparent 76%); }
        .barra-item { display:flex; flex:1; flex-direction:column; justify-content:flex-end; align-items:center; min-width:56px; height:100%; }
        .barra-valor { margin-bottom:8px; color:#e8f5e9; font-size:15px; font-weight:700; white-space:nowrap; }
        .barra { width:min(58px,82%); min-height:6px; height:var(--altura); border:1px solid rgba(165,214,167,.35); border-radius:10px 10px 3px 3px; background:linear-gradient(180deg,#66bb6a,#2e7d32); box-shadow:0 0 18px rgba(76,175,80,.2); transform-origin:bottom; animation:crecerBarra .8s cubic-bezier(.2,.8,.2,1) both; transition:filter .25s ease,transform .25s ease; }
        .barra:hover { filter:brightness(1.18); transform:scaleX(1.08); } .barra-mes { margin-top:10px; color:#a5b8aa; font-size:12px; font-weight:600; white-space:nowrap; }
        .sin-mediciones { padding:48px 25px; text-align:center; color:#a5b8aa; border:1px dashed rgba(165,214,167,.24); border-radius:16px; background:rgba(12,28,18,.58); } .sin-mediciones strong { display:block; margin-bottom:8px; color:#e8f5e9; font-size:18px; }
        @keyframes crecerBarra { from { transform:scaleY(0); } to { transform:scaleY(1); } } @keyframes entradaGrafico { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } } @keyframes pulsoMedicion { 50% { opacity:.55; box-shadow:0 0 22px rgba(102,187,106,1); } }
        @media (max-width:520px) { .grafico { padding:18px; } .area-barras { gap:10px; } .barra-valor { font-size:12px; } }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo">PURO</div>
    <div class="subtitle">Biofiltración Urbana</div>
    <a href="dashboard.php">Dashboard</a>
    <a href="biofiltros.php">Biofiltros</a>
    <a href="sensores.php">Sensores</a>
    <a href="mediciones.php" class="activo-menu">Mediciones</a>
    <a href="alertas.php">Alertas</a>
    <a href="reportes.php">Reportes</a>
    <a href="logout.php" class="cerrar-sesion">Cerrar sesión</a>
</div>
<main class="contenido">
    <div class="encabezado">
        <h1>Estadísticas de Mediciones</h1>
        <p>Promedios mensuales registrados por los sensores del sistema PURO.</p>
        <div class="resumen-mediciones">
            <span class="indicador-activo"></span>
            <span>Prototipo activo: <strong><?php echo htmlspecialchars($biofiltro_actual); ?></strong> · <?php echo htmlspecialchars((string) $total_mediciones); ?> mediciones analizadas</span>
        </div>
    </div>
    <?php if (!empty($graficos)): ?>
        <section class="graficos-contenedor" aria-label="Gráficos mensuales de mediciones">
            <?php foreach ($graficos as $tipo_sensor => $datos): ?>
                <?php
                $valores = array_column($datos, "valor_promedio");
                $valor_maximo = max(array_map("floatval", $valores));
                $unidad = $datos[0]["unidad"];
                ?>
                <article class="grafico">
                    <div class="grafico-cabecera">
                        <div><h2><?php echo htmlspecialchars(titulo_sensor($tipo_sensor)); ?></h2><p>Promedio de mediciones por mes</p></div>
                        <span class="unidad"><?php echo htmlspecialchars($unidad); ?></span>
                    </div>
                    <div class="area-barras">
                        <?php foreach ($datos as $indice => $dato): ?>
                            <?php
                            $valor = (float) $dato["valor_promedio"];
                            $altura = $valor_maximo > 0 ? ($valor / $valor_maximo) * 100 : 0;
                            ?>
                            <div class="barra-item">
                                <span class="barra-valor"><?php echo htmlspecialchars(number_format($valor, 2)); ?></span>
                                <div class="barra" style="--altura: <?php echo htmlspecialchars(number_format($altura, 2, ".", "")); ?>%; animation-delay: <?php echo htmlspecialchars((string) ($indice * 0.1)); ?>s;" title="<?php echo htmlspecialchars(titulo_sensor($tipo_sensor) . ": " . number_format($valor, 2) . " " . $unidad); ?>"></div>
                                <span class="barra-mes"><?php echo htmlspecialchars(nombre_mes($dato["mes"])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <section class="sin-mediciones"><strong>Aún no hay mediciones para mostrar.</strong>No existen registros asociados al prototipo PURO Centro.</section>
    <?php endif; ?>
</main>
</body>
</html>
