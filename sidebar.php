<?php
/**
 * PURO - Componente de Barra Lateral (Sidebar)
 * Diseño Glassmorphism con expansión al acercar el cursor (Hover).
 * Soporta roles:
 *  - Administrador / Usuario: 7 opciones (Dashboard, Biofiltros, Sensores, Mediciones, Alertas, Reportes, Cerrar sesión)
 *  - Invitado: 6 opciones (se oculta Reportes)
 */

$pagina_actual = $pagina_actual ?? '';
$es_invitado = !empty($_SESSION["es_invitado"]);
?>
<nav class="sidebar" id="sidebar" aria-label="Navegación principal">

    <!-- Encabezado / Logo con icono -->
    <div class="sidebar-header" id="menuToggle" title="PURO · Biofiltración Urbana">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                <path d="M12 22.5V13"/>
                <path d="M12 13l3-3"/>
                <path d="M12 17l-2.5-2.5"/>
            </svg>
        </div>
        <div class="logo-info">
            <span class="logo-name">PURO</span>
            <span class="logo-sub">Biofiltración Urbana</span>
        </div>
    </div>

    <!-- Lista de Navegación -->
    <ul class="sidebar-menu" id="menu">

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
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 9.2-1.5 5.5-4.5 8.8-9 8.8z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
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

        <!-- 6. Reportes (Solo para administradores / usuarios autenticados, no invitados) -->
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

    <!-- 7. Cerrar sesión (al final del sidebar) -->
    <div class="sidebar-footer">
        <a href="logout.php" class="cerrar-sesion" title="Cerrar sesión">
            <span class="icon logout-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            <span class="link-name">Cerrar sesión</span>
            <svg class="arr-1" viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7v-4H4v-2h9V5z"/></svg>
            <svg class="arr-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7v-4H4v-2h9V5z"/></svg>
            <span class="circle"></span>
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
