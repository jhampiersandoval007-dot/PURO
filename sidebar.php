<?php
/**
 * PURO - Sidebar Flotante Glassmorphism con Expansión Hover
 * Roles soportados:
 *   - Administrador: 7 opciones (Dashboard, Biofiltros, Sensores, Mediciones, Alertas, Reportes, Cerrar sesión)
 *   - Invitado: 6 opciones (se oculta Reportes)
 */
$pagina_actual = $pagina_actual ?? '';
$es_invitado = !empty($_SESSION["es_invitado"]);
?>
<style>
/* =========================================================
   PURO - SIDEBAR GLASSERMOPHISM INTERACTIVO (HOVER)
   ========================================================= */

/* Estructura base flotante centrada verticalmente */
.sidebar {
    position: fixed !important;
    top: 50% !important;
    left: 20px !important;
    transform: translateY(-50%) !important;
    width: 70px !important;
    height: auto !important;
    padding: 16px 10px !important;
    background: rgba(10, 32, 20, 0.78) !important;
    backdrop-filter: blur(18px) saturate(140%) !important;
    -webkit-backdrop-filter: blur(18px) saturate(140%) !important;
    border: 1px solid rgba(165, 214, 167, 0.25) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.15) !important;
    overflow: hidden !important;
    transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease !important;
    z-index: 9999 !important;
    box-sizing: border-box !important;
}

/* Expansión al acercar el cursor (Hover) o foco */
.sidebar:hover,
.sidebar:focus-within,
.sidebar.open {
    width: 240px !important;
    background: rgba(8, 26, 16, 0.94) !important;
    border-color: rgba(165, 214, 167, 0.42) !important;
    box-shadow: 0 25px 55px rgba(0, 0, 0, 0.65), 0 0 30px rgba(76, 175, 80, 0.18), inset 0 1px 1px rgba(255, 255, 255, 0.22) !important;
}

/* Encabezado / Logo */
.sidebar .logo {
    display: flex !important;
    align-items: center !important;
    gap: 16px !important;
    height: 46px !important;
    padding: 0 8px !important;
    margin-bottom: 14px !important;
    border-bottom: 1px solid rgba(165, 214, 167, 0.14) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    cursor: pointer !important;
    box-sizing: border-box !important;
}

.sidebar .logo-icon {
    min-width: 32px !important;
    width: 32px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #81c784 !important;
    flex-shrink: 0 !important;
}

.sidebar .logo-icon svg {
    width: 24px !important;
    height: 24px !important;
    max-width: 24px !important;
    max-height: 24px !important;
    display: block !important;
}

.sidebar .logo-name {
    font-size: 19px !important;
    font-weight: 800 !important;
    letter-spacing: 2px !important;
    color: #ffffff !important;
    opacity: 0 !important;
    pointer-events: none !important;
    transform: translateX(-10px) !important;
    transition: opacity 0.28s ease, transform 0.28s ease !important;
    white-space: nowrap !important;
}

.sidebar:hover .logo-name,
.sidebar:focus-within .logo-name,
.sidebar.open .logo-name {
    opacity: 1 !important;
    pointer-events: auto !important;
    transform: translateX(0) !important;
}

/* Lista de navegación */
.sidebar ul.menu-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 7px !important;
}

.sidebar ul.menu-list li {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
    position: relative !important;
}

.sidebar ul.menu-list a {
    display: flex !important;
    align-items: center !important;
    gap: 16px !important;
    height: 46px !important;
    padding: 0 9px !important;
    color: #c8d6cb !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    border-radius: 12px !important;
    border: 1px solid transparent !important;
    background: transparent !important;
    overflow: hidden !important;
    transition: all 0.25s ease !important;
    box-sizing: border-box !important;
}

.sidebar .icon {
    min-width: 32px !important;
    width: 32px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #a5d6a7 !important;
    flex-shrink: 0 !important;
    transition: color 0.25s ease, transform 0.25s ease !important;
}

.sidebar .icon svg {
    width: 20px !important;
    height: 20px !important;
    max-width: 20px !important;
    max-height: 20px !important;
    display: block !important;
}

.sidebar .link-name {
    font-size: 14px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
    opacity: 0 !important;
    pointer-events: none !important;
    transform: translateX(-10px) !important;
    transition: opacity 0.25s ease, transform 0.25s ease !important;
    white-space: nowrap !important;
}

.sidebar:hover .link-name,
.sidebar:focus-within .link-name,
.sidebar.open .link-name {
    opacity: 1 !important;
    pointer-events: auto !important;
    transform: translateX(0) !important;
}

/* Efecto hover en opciones individuales */
.sidebar ul.menu-list a:hover {
    color: #ffffff !important;
    background: rgba(102, 187, 106, 0.18) !important;
    border-color: rgba(165, 214, 167, 0.28) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
    transform: translateX(3px) !important;
}

.sidebar ul.menu-list a:hover .icon {
    color: #81c784 !important;
    transform: scale(1.1) !important;
}

/* Opción activa (Glassmorphism verde esmeralda) */
.sidebar ul.menu-list li.active a,
.sidebar ul.menu-list a.activo-menu {
    color: #ffffff !important;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.4), rgba(46, 125, 50, 0.22)) !important;
    border: 1px solid rgba(165, 214, 167, 0.45) !important;
    box-shadow: 0 6px 18px rgba(46, 125, 50, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.25) !important;
}

.sidebar ul.menu-list li.active a .icon,
.sidebar ul.menu-list a.activo-menu .icon {
    color: #ffffff !important;
    filter: drop-shadow(0 0 6px rgba(102, 187, 106, 0.8)) !important;
}

/* Botón Cerrar Sesión (Opción 7 / 6) */
.sidebar .logout-box {
    margin-top: 10px !important;
    padding-top: 10px !important;
    border-top: 1px solid rgba(165, 214, 167, 0.14) !important;
}

.sidebar a.btn-logout {
    display: flex !important;
    align-items: center !important;
    gap: 16px !important;
    height: 46px !important;
    padding: 0 9px !important;
    color: #ffb4ac !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    border-radius: 12px !important;
    border: 1px solid rgba(255, 138, 128, 0.25) !important;
    background: rgba(143, 55, 43, 0.24) !important;
    backdrop-filter: blur(12px) !important;
    overflow: hidden !important;
    transition: all 0.25s ease !important;
    box-sizing: border-box !important;
}

.sidebar a.btn-logout .icon {
    color: #ff8a80 !important;
}

.sidebar a.btn-logout:hover {
    color: #ffffff !important;
    background: linear-gradient(135deg, rgba(211, 47, 47, 0.55), rgba(183, 28, 28, 0.38)) !important;
    border-color: rgba(255, 138, 128, 0.6) !important;
    box-shadow: 0 6px 20px rgba(211, 47, 47, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.25) !important;
    transform: translateX(3px) !important;
}

.sidebar a.btn-logout:hover .icon {
    color: #ffffff !important;
    transform: scale(1.1) !important;
}

/* Desplazamiento del contenido para no chocar con el sidebar */
.contenido,
main {
    margin-left: 110px !important;
    transition: margin-left 0.35s ease !important;
}

/* Dispositivos móviles / pantallas estrechas */
@media (max-width: 800px) {
    .sidebar {
        top: auto !important;
        bottom: 12px !important;
        left: 10px !important;
        transform: none !important;
        width: 60px !important;
        padding: 10px 6px !important;
    }

    .sidebar.open {
        width: 230px !important;
    }

    .contenido,
    main {
        margin-left: 80px !important;
        padding: 20px 14px !important;
    }
}
</style>

<nav class="sidebar" id="sidebar" aria-label="Navegación lateral">

    <!-- Logo / Encabezado -->
    <div class="logo" id="menuToggle" title="PURO · Biofiltración Urbana">
        <span class="logo-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 9.2-1.5 5.5-4.5 8.8-9 8.8z"/>
                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
            </svg>
        </span>
        <span class="logo-name">PURO</span>
    </div>

    <!-- Opciones del menú -->
    <ul class="menu-list" id="menu">

        <!-- 1. Dashboard -->
        <li class="<?php echo $pagina_actual === 'dashboard' ? 'active' : ''; ?>">
            <a href="dashboard.php" class="<?php echo $pagina_actual === 'dashboard' ? 'activo-menu' : ''; ?>" title="Dashboard">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="9" rx="1"/>
                        <rect x="14" y="3" width="7" height="5" rx="1"/>
                        <rect x="14" y="12" width="7" height="9" rx="1"/>
                        <rect x="3" y="16" width="7" height="5" rx="1"/>
                    </svg>
                </span>
                <span class="link-name">Dashboard</span>
            </a>
        </li>

        <!-- 2. Biofiltros -->
        <li class="<?php echo $pagina_actual === 'biofiltros' ? 'active' : ''; ?>">
            <a href="biofiltros.php" class="<?php echo $pagina_actual === 'biofiltros' ? 'activo-menu' : ''; ?>" title="Biofiltros">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                        <path d="M12 22.5V13"/>
                        <path d="M12 13l3-3"/>
                        <path d="M12 17l-2.5-2.5"/>
                    </svg>
                </span>
                <span class="link-name">Biofiltros</span>
            </a>
        </li>

        <!-- 3. Sensores -->
        <li class="<?php echo $pagina_actual === 'sensores' ? 'active' : ''; ?>">
            <a href="sensores.php" class="<?php echo $pagina_actual === 'sensores' ? 'activo-menu' : ''; ?>" title="Sensores">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                        <rect x="9" y="9" width="6" height="6"/>
                        <line x1="9" y1="1" x2="9" y2="4"/>
                        <line x1="15" y1="1" x2="15" y2="4"/>
                        <line x1="9" y1="20" x2="9" y2="23"/>
                        <line x1="15" y1="20" x2="15" y2="23"/>
                        <line x1="20" y1="9" x2="23" y2="9"/>
                        <line x1="20" y1="14" x2="23" y2="14"/>
                        <line x1="1" y1="9" x2="4" y2="9"/>
                        <line x1="1" y1="14" x2="4" y2="14"/>
                    </svg>
                </span>
                <span class="link-name">Sensores</span>
            </a>
        </li>

        <!-- 4. Mediciones -->
        <li class="<?php echo $pagina_actual === 'mediciones' ? 'active' : ''; ?>">
            <a href="mediciones.php" class="<?php echo $pagina_actual === 'mediciones' ? 'activo-menu' : ''; ?>" title="Mediciones">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                        <line x1="2" y1="20" x2="22" y2="20"/>
                    </svg>
                </span>
                <span class="link-name">Mediciones</span>
            </a>
        </li>

        <!-- 5. Alertas -->
        <li class="<?php echo $pagina_actual === 'alertas' ? 'active' : ''; ?>">
            <a href="alertas.php" class="<?php echo $pagina_actual === 'alertas' ? 'activo-menu' : ''; ?>" title="Alertas">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </span>
                <span class="link-name">Alertas</span>
            </a>
        </li>

        <!-- 6. Reportes (Solo disponible para Administrador / Usuario, no para Invitado) -->
        <?php if (!$es_invitado): ?>
        <li class="<?php echo $pagina_actual === 'reportes' ? 'active' : ''; ?>">
            <a href="reportes.php" class="<?php echo $pagina_actual === 'reportes' ? 'activo-menu' : ''; ?>" title="Reportes">
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </span>
                <span class="link-name">Reportes</span>
            </a>
        </li>
        <?php endif; ?>

    </ul>

    <!-- 7. Cerrar sesión (6 para invitados) -->
    <div class="logout-box">
        <a href="logout.php" class="btn-logout" title="Cerrar sesión">
            <span class="icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            <span class="link-name">Cerrar sesión</span>
        </a>
    </div>

</nav>

<script>
(function() {
    const sidebar = document.getElementById("sidebar");
    const menuToggle = document.getElementById("menuToggle");
    if (menuToggle && sidebar) {
        menuToggle.addEventListener("click", function() {
            if (window.matchMedia("(hover: none)").matches || window.innerWidth <= 800) {
                sidebar.classList.toggle("open");
            }
        });
    }
})();
</script>
