# PURO – Sistema Inteligente de Biofiltración Urbana con Monitoreo IoT

## 1. Descripción del proyecto

PURO es un sistema de biofiltración urbana que utiliza microalgas como parte del proceso de biofiltración y captura de CO₂, complementado con monitoreo de variables ambientales mediante IoT.

## 2. Objetivo técnico

Desarrollar un prototipo de biofiltración con monitoreo mediante ESP32 y sensores, almacenando y visualizando mediciones en una plataforma web.

## 3. Tecnologías

- ESP32 DevKit y Arduino IDE; C/C++ para el microcontrolador.
- PHP, HTML, CSS, MySQL y MySQLi para la plataforma web.
- JavaScript solo cuando sea necesario.
- ProFreeHost puede utilizarse para alojamiento web cuando corresponda.

No introducir Laravel, React, Node.js ni otros frameworks salvo solicitud expresa.

## 4. Hardware actual

- ESP32 DevKit de 38 pines, DHT22, MQ-135, MG811 y relé de un canal de 5 V.
- Protoboard, cables Dupont y bomba.
- Sistema físico de biofiltración con microalgas.

## 5. Sensores y pines conocidos

| Componente | Uso | Conexión o consideración actual |
| --- | --- | --- |
| DHT22 | Temperatura y humedad relativa | VCC a 3.3 V, OUT a GPIO 4 y GND a GND. |
| MQ-135 | Indicador general de calidad/gases del aire | Lectura analógica por GPIO 34 cuando corresponda; DO no es necesario para mediciones analógicas. No se considera un medidor preciso de CO₂ en ppm sin calibración específica. |
| MG811 | Monitoreo dedicado de CO₂ | Posible lectura por GPIO 35. Requiere calibración según el módulo; comprobar que AO no exceda 3.3 V y usar divisor de tensión si hace falta. |

Para las lecturas analógicas con Wi‑Fi, preferir ADC1, especialmente GPIO 34 a GPIO 39. Evitar ADC2 mientras Wi‑Fi esté activo. Antes de cambiar pines, revisar el código de firmware existente: actualmente no hay archivos de firmware en esta raíz.

## 6. Comunicación y estado de integración

- La conexión USB sirve para programar el ESP32, usar el monitor serial y hacer pruebas.
- Está previsto que el ESP32 utilice Wi‑Fi para transmitir datos al sistema web.
- **Pendiente de implementación/verificación en esta raíz:** firmware ESP32, endpoint PHP de recepción y envío de mediciones desde el dispositivo.

Arquitectura prevista: `ESP32 + sensores → adquisición de datos → comunicación → servidor PHP → MySQL → plataforma web → visualización`.

## 7. Estructura web actual

| Archivo | Estado y función observada |
| --- | --- |
| `index.php` | Página básica de presentación de PURO. |
| `login.php` y `login.css` | Inicio de sesión y sus estilos. |
| `conexion.php` | Crea una conexión MySQLi a la base de datos `puro_db`. |
| `rate_limit.php` | Límite por sesión de 20 solicitudes en 10 segundos; actualmente se incluye desde `dashboard.php`. |
| `dashboard.php` | Panel que muestra la última medición de CO2, temperatura y humedad. |
| `biofiltros.php`, `sensores.php`, `mediciones.php`, `alertas.php`, `reportes.php` | Páginas autenticadas de consulta y visualización de registros. |
| `logout.php` | Cierra la sesión y redirige a `/PURO/login.php`. |

Las páginas del panel usan una barra lateral con enlaces a las seis secciones y estilos CSS embebidos. No se han observado archivos JavaScript en la raíz.

## 8. Base de datos: evidencia disponible en el código

Las tablas usadas por las consultas son `roles`, `usuarios`, `biofiltros`, `tipos_sensores`, `sensores`, `mediciones`, `alertas` y `reportes`.

Columnas observadas (no sustituyen una inspección real del esquema):

- `usuarios`: `id_usuario`, `nombre`, `correo`, `password`, `id_rol`.
- `roles`: `id_rol`, `nombre_rol`.
- `biofiltros`: `id_biofiltro`, `nombre`, `ubicacion`, `estado`, `fecha_instalacion`, `descripcion`, `id_usuario`.
- `tipos_sensores`: `id_tipo_sensor`, `nombre`.
- `sensores`: `id_sensor`, `id_tipo_sensor`, `id_biofiltro`, `modelo`, `estado`, `fecha_instalacion`.
- `mediciones`: `id_medicion`, `id_sensor`, `valor`, `unidad`, `fecha_hora`.
- `alertas`: `id_alerta`, `id_medicion`, `tipo_alerta`, `descripcion`, `nivel`, `fecha_hora`.
- `reportes`: `id_reporte`, `id_usuario`, `titulo`, `descripcion`, `fecha_generacion`, `formato`.

Relaciones utilizadas: un sensor se asocia con un tipo de sensor y un biofiltro; una medición se asocia con un sensor; una alerta se asocia con una medición; un biofiltro y un reporte se relacionan con usuarios; un usuario se relaciona con un rol. Revisar el esquema real antes de modificar SQL.

## 9. Login, sesión y seguridad

**Implementado:** `login.php` usa sesiones, una consulta preparada MySQLi por correo, contador de intentos en sesión, bloqueo de 60 segundos después de cinco fallos y `session_regenerate_id(true)` al autenticar correctamente. Los mensajes y valores de salida en las vistas se escapan con `htmlspecialchars()`.

**Atención:** la comparación actual de contraseña es directa (`$password === $usuario["password"]`); por ello no se debe afirmar que las contraseñas ya estén protegidas con hash. Antes de migrar, confirmar el formato almacenado y planificar el cambio a `password_hash()` y `password_verify()` sin invalidar usuarios existentes. No almacenar contraseñas nuevas en texto plano.

Mantener consultas preparadas, validación de entradas, sesiones y escape de contenido mostrado al usuario. No exponer ni copiar credenciales de `conexion.php` fuera de la configuración local.

## 10. Dashboard y visualización

El dashboard consulta la última fila de `mediciones` para tipos cuyo nombre sea exactamente `CO2`, `Temperatura` y `Humedad`, ordenando por `fecha_hora` descendente. Si no hay resultados, muestra `Sin datos`. También presenta un estado visual fijo para “PURO Norte”; no se ha verificado que ese texto provenga de la base de datos.

Las secciones de consulta muestran los registros existentes; no se han observado formularios de creación, edición, eliminación, generación de reportes ni reglas automáticas de alertas en estos archivos.

## 11. Diseño visual

La interfaz utiliza tonos verdes, verde oscuro, gris/blanco y una apariencia limpia de monitoreo ambiental. El login incorpora el logo PURO, la descripción “Sistema Inteligente de Biofiltración Urbana”, campos de correo y contraseña, el único botón “Ingresar al sistema”, estado “Sistema activo”, “Monitoreo ambiental” y el pie “PURO · Biofiltración Urbana”. El botón incluye hojas SVG decorativas.

No agregar botones ni funcionalidades no solicitadas.

## 12. Biofiltro y bomba

El sistema físico utiliza microalgas para biofiltración; la especie considerada es *Chlorella*, especialmente *Chlorella vulgaris* cuando corresponda. El recipiente debe permitir luz e intercambio gaseoso, con protección contra contaminación y salpicaduras; no debe sellarse herméticamente si ello impide dicho intercambio.

Antes de controlar una bomba desde el ESP32, verificar su función y requisitos eléctricos: bomba de agua para circulación de líquido, bomba de aire para suministro de aire y piedra difusora para distribuir aire. Usar relé o etapa de control adecuada y comprobar voltaje y corriente.

## 13. Estado funcional

| Estado | Elementos |
| --- | --- |
| **Implementado en la raíz actual** | Login, cierre de sesión, consultas MySQLi y vistas para dashboard, biofiltros, sensores, mediciones, alertas y reportes. |
| **En desarrollo / pendiente de verificación** | Integración efectiva ESP32-Wi‑Fi-servidor, firmware, recepción de datos, calibración del MG811 y lectura real de los sensores. |
| **Planificado** | Autenticación, gestión de biofiltros y sensores, registro y visualización de mediciones, alertas y reportes; algunas secciones ya visualizan datos, pero no se debe asumir que toda su gestión está implementada. |

## 14. Reglas para desarrollos futuros

1. Revisar primero los archivos y la base de datos reales.
2. Mantener nombres, rutas y funcionalidades existentes; no inventar tablas, columnas, tecnologías o endpoints.
3. Usar código sencillo, explicable y sin dependencias innecesarias.
4. Antes de cambiar firmware, revisar pines, alimentación, sensores y Wi‑Fi; conservar el monitor serial para pruebas y evitar `delay` innecesarios en monitoreo continuo.
5. Explicar brevemente los cambios e indicar cómo probarlos.
6. Ante un error, identificar primero su causa antes de modificar varios componentes.

## 15. Alcance de Codex

Codex se concentra en código, depuración, estructura de archivos, base de datos, integración ESP32, sensores, PHP, HTML, CSS, JavaScript y pruebas técnicas. La documentación académica, capítulos de tesis, redacción, diagramas y formato APA se trabajan principalmente fuera de este entorno.

## 16. Regla principal

PURO es un proyecto académico en desarrollo. Una característica planificada no equivale a una característica implementada. Ante cualquier duda, verificar primero los archivos y el esquema real antes de responder o modificar código.
