<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT
            a.id_alerta,
            a.tipo_alerta,
            a.descripcion,
            a.nivel,
            a.fecha_hora,
            m.valor,
            m.unidad,
            ts.nombre AS tipo_sensor,
            b.nombre AS biofiltro
        FROM alertas a
        INNER JOIN mediciones m
            ON a.id_medicion = m.id_medicion
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
    <link rel="stylesheet" href="panel.css">
</head>

<body>

<div class="sidebar">

    <div class="logo">PURO</div>
    <div class="subtitle">Biofiltración Urbana</div>

    <a href="dashboard.php">Dashboard</a>
    <a href="biofiltros.php">Biofiltros</a>
    <a href="sensores.php">Sensores</a>
    <a href="mediciones.php">Mediciones</a>
    <a href="alertas.php" class="activo-menu">Alertas</a>
    <?php if (empty($_SESSION["es_invitado"])): ?>
        <a href="reportes.php">Reportes</a>
    <?php endif; ?>

    <a href="logout.php" class="cerrar-sesion">
        Cerrar sesión
    </a>

</div>

<div class="contenido">

    <div class="encabezado">
        <h1>Alertas Ambientales</h1>
        <p>Alertas detectadas durante el monitoreo del sistema PURO</p>
    </div>

    <div class="tabla-contenedor">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alerta</th>
                    <th>Biofiltro</th>
                    <th>Sensor</th>
                    <th>Medición</th>
                    <th>Nivel</th>
                    <th>Descripción</th>
                    <th>Fecha y hora</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($resultado && $resultado->num_rows > 0): ?>

                <?php while ($alerta = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($alerta["id_alerta"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($alerta["tipo_alerta"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($alerta["biofiltro"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($alerta["tipo_sensor"]); ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $alerta["valor"] . " " . $alerta["unidad"]
                            );
                            ?>
                        </td>

                        <td class="nivel nivel-<?php echo htmlspecialchars($alerta["nivel"]); ?>">
                            <?php echo htmlspecialchars($alerta["nivel"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($alerta["descripcion"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($alerta["fecha_hora"]); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="sin-datos">
                        No existen alertas registradas.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
