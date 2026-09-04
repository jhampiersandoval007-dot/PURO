<?php
session_start();
require_once "conexion.php";

$mensaje = "";

// Configuración contra fuerza bruta
$max_intentos = 5;
$tiempo_bloqueo = 60; // 60 segundos

// Crear contador si todavía no existe
if (!isset($_SESSION["intentos_login"])) {
    $_SESSION["intentos_login"] = 0;
}

// Comprobar si existe un bloqueo
if (isset($_SESSION["bloqueado_hasta"])) {

    $tiempo_restante = $_SESSION["bloqueado_hasta"] - time();

    if ($tiempo_restante <= 0) {
        unset($_SESSION["bloqueado_hasta"]);
        $_SESSION["intentos_login"] = 0;
    }
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Acceso de consulta sin crear un usuario en la base de datos
    if (isset($_POST["acceso_invitado"])) {

        $_SESSION["intentos_login"] = 0;
        unset($_SESSION["bloqueado_hasta"]);

        $_SESSION["id_usuario"] = 0;
        $_SESSION["nombre"] = "Invitado";
        $_SESSION["id_rol"] = null;
        $_SESSION["es_invitado"] = true;

        session_regenerate_id(true);

        header("Location: dashboard.php");
        exit();

    // Verificar primero si está bloqueado
    } elseif (
        isset($_SESSION["bloqueado_hasta"]) &&
        time() < $_SESSION["bloqueado_hasta"]
    ) {

        $tiempo_restante = $_SESSION["bloqueado_hasta"] - time();

        $mensaje = "Demasiados intentos fallidos. Intente nuevamente en "
                 . $tiempo_restante . " segundos.";

    } else {

        $correo = $_POST["correo"];
        $password = $_POST["password"];

        // Consulta preparada contra SQL Injection
        $sql = "SELECT id_usuario, nombre, correo, password, id_rol
                FROM usuarios
                WHERE correo = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();

        $resultado = $stmt->get_result();

        $login_correcto = false;

        if ($resultado->num_rows === 1) {

            $usuario = $resultado->fetch_assoc();

            if ($password === $usuario["password"]) {
                $login_correcto = true;
            }
        }

        // LOGIN CORRECTO
        if ($login_correcto) {

            // Reiniciar intentos fallidos
            $_SESSION["intentos_login"] = 0;
            unset($_SESSION["bloqueado_hasta"]);

            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["id_rol"] = $usuario["id_rol"];
            unset($_SESSION["es_invitado"]);

            // Renovar ID de sesión después del login
            session_regenerate_id(true);

            header("Location: dashboard.php");
            exit();

        } else {

            // LOGIN INCORRECTO
            $_SESSION["intentos_login"]++;

            if ($_SESSION["intentos_login"] >= $max_intentos) {

                $_SESSION["bloqueado_hasta"] = time() + $tiempo_bloqueo;

                $mensaje = "Demasiados intentos fallidos. "
                         . "Acceso bloqueado durante 60 segundos.";

            } else {

                $intentos_restantes =
                    $max_intentos - $_SESSION["intentos_login"];

                $mensaje = "Correo o contraseña incorrectos. "
                         . "Intentos restantes: "
                         . $intentos_restantes . ".";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>PURO - Iniciar Sesión</title>

    <!-- Estilos del Login -->
    <link rel="stylesheet" href="login.css">

</head>

<body>

    <div class="login-container">

        <!-- LOGO Y NOMBRE DEL SISTEMA -->
        <div class="logo-area">

            <div class="logo-icon">
                ◉
            </div>

            <h1>PURO</h1>

            <h2>
                Sistema Inteligente de Biofiltración Urbana
            </h2>

            <div class="linea"></div>

        </div>


        <!-- TÍTULO -->
        <h3>
            Acceso al sistema
        </h3>


        <!-- MENSAJES DE ERROR / BLOQUEO -->
        <?php if ($mensaje != ""): ?>

            <div class="mensaje">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

        <?php endif; ?>


        <!-- FORMULARIO -->
        <form method="POST" action="">

            <!-- CORREO -->
            <div class="form-grupo">

                <label for="correo">
                    Usuario (correo electrónico)
                </label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="usuario@puro.com"
                    autocomplete="email"
                    required
                >

            </div>


            <!-- CONTRASEÑA -->
            <div class="form-grupo">

                <label for="password">
                    Contraseña
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingrese su contraseña"
                    autocomplete="current-password"
                    required
                >

            </div>


            <!-- BOTÓN ÚNICO -->
            <button
                type="submit"
                class="btn-ingresar"
            >

                <span>
                    Ingresar como administrador
                </span>

                <!-- HOJA 1 -->
                <svg
                    class="icon-1"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        class="fil-leaf-1"
                        d="M12 21C8 17 5 13 6 8c4 1 7 4 6 8-1 2-2 4 0 5z"
                    />
                </svg>


                <!-- HOJA 2 -->
                <svg
                    class="icon-2"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        class="fil-leaf-2"
                        d="M12 21C16 17 19 13 18 8c-4 1-7 4-6 8 1 2 2 4 0 5z"
                    />
                </svg>


                <!-- HOJA 3 -->
                <svg
                    class="icon-3"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        class="fil-leaf-3"
                        d="M12 20C9 16 8 12 10 8c3 2 5 5 3 9z"
                    />
                </svg>


                <!-- HOJA 4 -->
                <svg
                    class="icon-4"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        class="fil-leaf-4"
                        d="M12 20C15 16 16 12 14 8c-3 2-5 5-3 9z"
                    />
                </svg>


                <!-- HOJA 5 -->
                <svg
                    class="icon-5"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        class="fil-leaf-5"
                        d="M12 18c-2-4-2-7 0-10 2 3 2 6 0 10z"
                    />
                </svg>

            </button>

        </form>

        <div class="separador-acceso">
            <span>o</span>
        </div>

        <form method="POST" action="" class="form-invitado">
            <button
                type="submit"
                name="acceso_invitado"
                value="1"
                class="btn-ingresar"
            >
                <span>Ingresar como invitado</span>
            </button>
        </form>

        <p class="texto-invitado">
            Acceso directo para consultar el monitoreo ambiental.
        </p>


        <!-- INFORMACIÓN DEL SISTEMA -->
        <div class="sistema-info">

            <div class="estado-sistema">

                <span class="punto-activo"></span>

                Sistema activo

            </div>

            <div>
                Monitoreo ambiental
            </div>

        </div>


        <!-- PIE -->
        <div class="footer-login">

            PURO · Biofiltración Urbana

        </div>

    </div>

</body>

</html>
