<?php
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido."]);
    exit();
}

require_once "conexion.php";
require_once "iot_config.php";

// Verificación de la clave enviada por el ESP32
if (!hash_equals($clave_iot, $_POST["clave"] ?? "")) {
    http_response_code(401);
    echo json_encode(["ok" => false, "mensaje" => "No autorizado."]);
    exit();
}

// Lectura y validación de variables POST recibidas desde el ESP32
$temperatura = filter_input(INPUT_POST, "temperatura", FILTER_VALIDATE_FLOAT);
$humedad = filter_input(INPUT_POST, "humedad", FILTER_VALIDATE_FLOAT);
$calidad_aire = filter_input(INPUT_POST, "calidad_aire", FILTER_VALIDATE_INT);
$co2 = filter_input(INPUT_POST, "co2", FILTER_VALIDATE_INT);

if ($temperatura === false || $humedad === false || $calidad_aire === false || 
    $temperatura < -40 || $temperatura > 125 || 
    $humedad < 0 || $humedad > 100 || 
    $calidad_aire < 0 || $calidad_aire > 4095) {
    http_response_code(422);
    echo json_encode(["ok" => false, "mensaje" => "Datos de medición no válidos."]);
    exit();
}

$co2_recibido = $co2 !== null;

if ($co2_recibido && ($co2 === false || $co2 < 0 || $co2 > 4095)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "mensaje" => "Lectura de CO2 no válida."]);
    exit();
}

$mediciones = [
    "Calidad del aire" => [$calidad_aire, "ADC"],
    "Temperatura" => [$temperatura, "°C"],
    "Humedad" => [$humedad, "%"]
];

if ($co2_recibido) {
    $mediciones["CO2"] = [$co2, "ADC"];
}

$sql_sensor = "SELECT s.id_sensor FROM sensores s INNER JOIN tipos_sensores ts ON s.id_tipo_sensor = ts.id_tipo_sensor WHERE ts.nombre = ? LIMIT 1";
$sql_insertar = "INSERT INTO mediciones (id_sensor, valor, unidad, fecha_hora) VALUES (?, ?, ?, NOW())";

try {
    $conexion->begin_transaction();
    $buscar_sensor = $conexion->prepare($sql_sensor);
    $insertar_medicion = $conexion->prepare($sql_insertar);

    foreach ($mediciones as $tipo_sensor => [$valor, $unidad]) {
        $buscar_sensor->bind_param("s", $tipo_sensor);
        $buscar_sensor->execute();
        $sensor = $buscar_sensor->get_result()->fetch_assoc();

        if (!$sensor) {
            throw new RuntimeException("Sensor no registrado.");
        }

        $id_sensor = (int) $sensor["id_sensor"];
        $valor_numerico = (float) $valor;

        $insertar_medicion->bind_param("ids", $id_sensor, $valor_numerico, $unidad);
        $insertar_medicion->execute();
    }

    $conexion->commit();
    echo json_encode([
        "ok" => true,
        "mensaje" => count($mediciones) . " mediciones registradas."
    ]);
} catch (Throwable $error) {
    $conexion->rollback();
    http_response_code(500);
    echo json_encode(["ok" => false, "mensaje" => "No se pudieron registrar las mediciones."]);
}
?>
