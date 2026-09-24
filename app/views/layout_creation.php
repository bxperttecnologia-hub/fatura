<?php
require_once '../app/helpers/translation.php';
require_once '../app/config/db.php';
require_once '../app/helpers/functions.php';
require_once '../app/helpers/authentication.php';
require_once '../app/views/head.php';

// Página atual (usada pra destacar o item ativo no menu
// e pra permitir requisições AJAX diretas na mesma rota)
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>


<style>
    .app-wrapper {
        display: flex;
    }

    .main-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Indicador de carregamento entre trocas de página SPA */
    #spa-loading-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0%;
        background: #007abd;
        z-index: 2000;
        transition: width 0.2s ease;
    }

    #spa-loading-bar.active {
        width: 70%;
    }

    #spa-loading-bar.done {
        width: 100%;
        opacity: 0;
        transition: width 0.2s ease, opacity 0.3s ease 0.2s;
    }
</style>

<div id="spa-loading-bar"></div>

<div class="app-wrapper">

    <!-- SIDEBAR -->
    <?php require_once '../app/views/side.php'; ?>

    <div class="main-wrapper">

        <!-- NAVBAR -->
        <?php require_once '../app/views/nav.php'; ?>

        <!-- CONTEÚDO REAL DA PÁGINA - único main do projeto -->
        <main id="app-main" style="width: 100% !important;" data-page="<?php echo htmlspecialchars($currentPage); ?>">
            <?php
            // Se a própria página que chamou o layout já definiu
            // $content, ele é injetado aqui. Isso permite tanto
            // load normal (F5 / primeira visita) quanto SPA.
            if (isset($content)) {
                echo $content;
            }
            ?>
        </main>
    </div>
</div>


<!-- Script central de layout: coordena sidebar + header + main -->
<script>
    const init = () => {
        const sidebar = document.getElementById('sidebar');

        if (!sidebar) return;

        const mainEl = document.querySelector('.main-wrapper');
        const headerEl = document.querySelector('.app-navbar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const menuDownIcons = document.querySelectorAll('.menu-link-icon');

        /**
         * Aplica o estado collapsed da sidebar
         */

        const applySidebarState = (collapsed) => {
            sidebar.classList.toggle('collapsed', collapsed);
            mainEl?.classList.toggle('collapsed', collapsed);
            headerEl?.classList.toggle('collapsed', collapsed);

            localStorage.setItem(
                'sidebarCollapsed',
                String(collapsed)
            );

            // Atualiza o ícone do botão
            const icon = toggleBtn?.querySelector('i, svg');

            if (icon) {
                icon.setAttribute(
                    'data-lucide',
                    collapsed ?
                    'panel-left-open' :
                    'panel-left-close'
                );
            }

            if (collapsed) {
                menuDownIcons.forEach((icon) => {
                    icon.classList.add('d-none');
                });
            } else {
                menuDownIcons.forEach((icon) => {
                    icon.classList.remove('d-none');
                });
            }

            // Recria os ícones Lucide
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };



        /**
         * Alterna entre sidebar desktop/mobile
         */
        const toggleSidebar = (e) => {
            e?.stopPropagation();

            if (window.innerWidth <= 992) {
                if (sidebar.classList.contains('show')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }

                return;
            }

            const collapsed = sidebar.classList.contains('collapsed');

            applySidebarState(!collapsed);
        };

        // ==========================================
        // Estado inicial desktop
        // ==========================================

        const savedState =
            localStorage.getItem('sidebarCollapsed') === 'true';

        applySidebarState(savedState);

        // ==========================================
        // Eventos dos botões
        // ==========================================

        toggleBtn?.addEventListener('click', toggleSidebar);

        mobileMenuBtn?.addEventListener('click', toggleSidebar);

        // ==========================================
        // Fechar menu ao clicar num link no mobile
        // ==========================================

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 992) {
                    closeMobileMenu();
                }
            });
        });

        // ==========================================
        // Resize da janela
        // ==========================================

        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) {
                closeMobileMenu();
            }
        });

        // Disponibiliza globalmente
        window.__closeMobileMenu = closeMobileMenu;
    };

    // ==========================================
    // Inicialização
    // ==========================================

    document.addEventListener('DOMContentLoaded', () => {
        init();

        // Marca o item ativo do menu na carga inicial da página
        if (
            window.SpaRouter &&
            typeof window.SpaRouter.highlightActiveLink === 'function'
        ) {
            window.SpaRouter.highlightActiveLink();
        }
    });
</script>
