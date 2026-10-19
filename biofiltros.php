<?php
require_once "auth_check.php";
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION["nombre"];

$sql = "SELECT 
            b.id_biofiltro,
            b.nombre,
            b.ubicacion,
            b.estado,
            NULL AS fecha_instalacion,
            b.descripcion,
            '-' AS responsable
        FROM biofiltros b
        ORDER BY b.id_biofiltro ASC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biofiltros - PURO</title>
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

        .estado-activo {
            color: #16824b;
            font-weight: bold;
        }

        .sin-datos {
            text-align: center;
            padding: 30px;
        }
    </style>
    <link rel="stylesheet" href="panel.css?v=20261019-glass-sidebar">
</head>

<body>

<?php $pagina_actual = 'biofiltros'; require_once "sidebar.php"; ?>

<div class="contenido">

    <div class="encabezado">
        <span class="etiqueta-panel">Prototipo urbano</span>
        <h1>Gestión de Biofiltros</h1>
        <p>Prototipo registrado en el sistema PURO.</p>
    </div>

    <div class="tabla-contenedor">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Instalación</th>
                    <th>Responsable</th>
                    <th>Descripción</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($resultado && $resultado->num_rows > 0): ?>

                <?php while ($biofiltro = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($biofiltro["id_biofiltro"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($biofiltro["nombre"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($biofiltro["ubicacion"]); ?>
                        </td>

                        <td class="estado-activo">
                            <?php echo htmlspecialchars($biofiltro["estado"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($biofiltro["fecha_instalacion"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($biofiltro["responsable"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($biofiltro["descripcion"]); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="sin-datos">
                        No existen biofiltros registrados.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
