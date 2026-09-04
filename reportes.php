<?php
session_start();
require_once "conexion.php";

// Verificar que exista una sesión activa
if (!isset($_SESSION["id_usuario"]) || !empty($_SESSION["es_invitado"])) {
    header("Location: dashboard.php");
    exit();
}

// Obtener los reportes junto con el usuario y su rol
$sql = "SELECT
            rp.id_reporte,
            rp.titulo,
            rp.descripcion,
            rp.fecha_generacion,
            rp.formato,
            u.nombre AS usuario,
            r.nombre_rol AS rol
        FROM reportes rp
        INNER JOIN usuarios u
            ON rp.id_usuario = u.id_usuario
        INNER JOIN roles r
            ON u.id_rol = r.id_rol
        ORDER BY rp.fecha_generacion DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reportes - PURO</title>

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

        /* Botón cerrar sesión */
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

        .formato {
            font-weight: bold;
            color: #123c32;
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
    <a href="alertas.php">Alertas</a>
    <a href="reportes.php" class="activo-menu">Reportes</a>

    <a href="logout.php" class="cerrar-sesion">
        Cerrar sesión
    </a>

</div>


<div class="contenido">

    <div class="encabezado">

        <h1>Reportes del Sistema</h1>

        <p>
            Informes generados durante el monitoreo ambiental de PURO
        </p>

    </div>


    <div class="tabla-contenedor">

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Formato</th>
                    <th>Generado por</th>
                    <th>Rol</th>
                    <th>Fecha de generación</th>
                </tr>

            </thead>


            <tbody>

            <?php if ($resultado && $resultado->num_rows > 0): ?>

                <?php while ($reporte = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($reporte["id_reporte"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($reporte["titulo"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($reporte["descripcion"]); ?>
                        </td>

                        <td class="formato">
                            <?php echo htmlspecialchars($reporte["formato"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($reporte["usuario"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($reporte["rol"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($reporte["fecha_generacion"]); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td colspan="7" class="sin-datos">
                        No existen reportes registrados.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
