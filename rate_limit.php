<?php

// Iniciar sesión solo si todavía no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración del límite
$limite_solicitudes = 20;
$periodo = 10; // segundos

$ahora = time();

// Crear registro inicial
if (!isset($_SESSION["rate_limit"])) {

    $_SESSION["rate_limit"] = [
        "inicio" => $ahora,
        "solicitudes" => 1
    ];

} else {

    $tiempo_transcurrido =
        $ahora - $_SESSION["rate_limit"]["inicio"];

    // Si terminó el periodo, reiniciar contador
    if ($tiempo_transcurrido >= $periodo) {

        $_SESSION["rate_limit"] = [
            "inicio" => $ahora,
            "solicitudes" => 1
        ];

    } else {

        // Aumentar número de solicitudes
        $_SESSION["rate_limit"]["solicitudes"]++;

        // Bloquear si supera el límite
        if ($_SESSION["rate_limit"]["solicitudes"] > $limite_solicitudes) {

            http_response_code(429);

            $espera = $periodo - $tiempo_transcurrido;

            die(
                "Demasiadas solicitudes. Intente nuevamente en "
                . $espera
                . " segundos."
            );
        }
    }
}