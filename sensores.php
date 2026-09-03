<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT
            s.id_sensor,
            ts.nombre AS tipo_sensor,
            s.modelo,
            s.estado,
            s.fecha_instalacion,
            b.nombre AS biofiltro
        FROM sensores s
        INNER JOIN tipos_sensores ts
            ON s.id_tipo_sensor = ts.id_tipo_sensor
        INNER JOIN biofiltros b
            ON s.id_biofiltro = b.id_biofiltro
        ORDER BY s.id_sensor ASC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores - PURO</title>

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

        .activo {
            color: #16824b;
            font-weight: bold;
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
    <a href="sensores.php" class="activo-menu">Sensores</a>
    <a href="mediciones.php">Mediciones</a>
    <a href="alertas.php">Alertas</a>
    <a href="reportes.php">Reportes</a>

    <a href="logout.php" class="cerrar-sesion">
        Cerrar sesión
    </a>

</div>


<div class="contenido">

    <div class="encabezado">
        <h1>Gestión de Sensores</h1>
        <p>Sensores instalados en los biofiltros del sistema PURO</p>
    </div>


    <div class="tabla-contenedor">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo de sensor</th>
                    <th>Modelo</th>
                    <th>Biofiltro</th>
                    <th>Estado</th>
                    <th>Fecha de instalación</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($resultado && $resultado->num_rows > 0): ?>

                <?php while ($sensor = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($sensor["id_sensor"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($sensor["tipo_sensor"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($sensor["modelo"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($sensor["biofiltro"]); ?>
                        </td>

                        <td class="activo">
                            <?php echo htmlspecialchars($sensor["estado"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($sensor["fecha_instalacion"]); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="sin-datos">
                        No existen sensores registrados.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
