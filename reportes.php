<?php
require_once "auth_check.php";
require_once "conexion.php";

// Verificar que exista una sesión activa
if (!isset($_SESSION["id_usuario"]) || !empty($_SESSION["es_invitado"])) {
    header("Location: dashboard.php");
    exit();
}

$mensaje = $_SESSION["mensaje_reporte"] ?? "";
unset($_SESSION["mensaje_reporte"]);

if (empty($_SESSION["token_csrf_reportes"])) {
    $_SESSION["token_csrf_reportes"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["generar_reporte"])) {
    $token = $_POST["token_csrf"] ?? "";

    if (!hash_equals($_SESSION["token_csrf_reportes"], $token)) {
        $_SESSION["mensaje_reporte"] = "No se pudo validar la solicitud. Vuelve a intentarlo.";
    } else {
        try {
            $resumen_mediciones = $conexion->query(
                "SELECT COUNT(*) AS total, MAX(fecha_hora) AS ultima FROM mediciones"
            )->fetch_assoc();
            $total_alertas = (int) $conexion->query(
                "SELECT COUNT(*) AS total FROM alertas"
            )->fetch_assoc()["total"];

            $fecha_generacion = date("Y-m-d H:i:s");
            $titulo = "Resumen de monitoreo · " . date("d/m/Y H:i", strtotime($fecha_generacion));
            $ultima_medicion = $resumen_mediciones["ultima"]
                ? date("d/m/Y H:i", strtotime($resumen_mediciones["ultima"]))
                : "Sin mediciones registradas";
            $descripcion = sprintf(
                "Resumen del sistema PURO: %d mediciones registradas, %d alertas registradas. Última medición: %s. Este reporte resume los registros disponibles al momento de su generación.",
                (int) $resumen_mediciones["total"],
                $total_alertas,
                $ultima_medicion
            );

            $stmt = $conexion->prepare(
                "INSERT INTO reportes (titulo, descripcion, fecha_generacion, id_usuario) VALUES (?, ?, ?, ?)"
            );
            $id_usuario = (int) $_SESSION["id_usuario"];
            $stmt->bind_param("sssi", $titulo, $descripcion, $fecha_generacion, $id_usuario);
            $stmt->execute();
            $stmt->close();

            $_SESSION["mensaje_reporte"] = "Reporte generado y guardado correctamente.";
        } catch (Throwable $error) {
            $_SESSION["mensaje_reporte"] = "No se pudo generar el reporte. Revisa la conexión con la base de datos.";
        }
    }

    header("Location: reportes.php");
    exit();
}

// Obtener los reportes junto con el usuario y su rol
$sql = "SELECT
            rp.id_reporte,
            rp.titulo,
            rp.descripcion,
            rp.fecha_generacion,
            u.nombre AS usuario,
            r.nombre AS rol
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

        .formato {
            font-weight: bold;
            color: #123c32;
        }

        .sin-datos {
            text-align: center;
            padding: 30px;
        }

        .acciones-reportes {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 0 0 22px;
            flex-wrap: wrap;
        }

        .btn-generar-reporte {
            padding: 12px 17px;
            border: 1px solid rgba(183, 235, 190, .34);
            border-radius: 12px;
            color: #f3fff4;
            background: linear-gradient(125deg, rgba(76, 150, 83, .42), rgba(36, 92, 50, .5));
            box-shadow: inset 0 1px rgba(255, 255, 255, .12), 0 8px 20px rgba(0, 0, 0, .15);
            font: inherit;
            font-weight: 650;
            cursor: pointer;
            transition: transform .2s ease, border-color .2s ease, background .2s ease;
        }

        .btn-generar-reporte:hover {
            transform: translateY(-2px);
            border-color: rgba(183, 235, 190, .65);
            background: linear-gradient(125deg, rgba(88, 170, 96, .55), rgba(42, 105, 57, .62));
        }

        .btn-generar-reporte:focus-visible {
            outline: 2px solid #b6edba;
            outline-offset: 3px;
        }

        .mensaje-reportes {
            color: #c8f0cc;
            font-size: 14px;
        }
    </style>
    <link rel="stylesheet" href="panel.css?v=20261019-glass-sidebar">
</head>

<body>

<?php $pagina_actual = 'reportes'; require_once "sidebar.php"; ?>

</body>
</html>
