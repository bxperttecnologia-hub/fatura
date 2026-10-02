/**
 * BXpert SPA Router
 * 
 * Único router consolidado da aplicação.
 * Responsabilidade: Rotas, navegação, history API, link interception.
 * 
 * Consolidação de:
 * - public/assets/js/router.js (antigo)
 * - public/assets/js/spa_router.js (refatorado)
 * - app1.js (removido - tinha função loadRoute() inexistente)
 */

const BXpertRouter = (function () {
  'use strict';

  // ═══════════════════════════════════════════════════════════════════════
  // CONFIG
  // ═══════════════════════════════════════════════════════════════════════

  const config = {
    mainSelector: '#app-main',
    loadingBarSelector: '#spa-loading-bar',
    linkSelector: 'a[data-spa]',
    sidebarSelector: '#sidebar',
    xRequestedWith: 'XMLHttpRequest'
  };

  const routes = {
    '/': { view: 'index.php', name: 'dashboard' },
    '/contacts': { view: 'contacts.php', name: 'contacts' },
    '/contacts/create': { view: 'register_contact.php', name: 'register_contact' },
    '/items': { view: 'items.php', name: 'items' },
    '/invoices': { view: 'list_invoices.php', name: 'invoices' },
    '/invoices/create': { view: 'create_invoices.php', name: 'create_invoice' },
    '/invoices/view': { view: 'invoice.php', name: 'invoice_view' },
    '/proformas': { view: 'list_proforms.php', name: 'proformas' },
    '/proformas/create': { view: 'create_proform.php', name: 'create_proform' },
    '/proformas/view': { view: 'proform.php', name: 'proform_view' },
    '/stock': { view: 'stock.php', name: 'stock' },
    '/employees': { view: 'employees.php', name: 'employees' },
    '/ponto': { view: 'ponto.php', name: 'ponto' },
    '/vacations': { view: 'vacations.php', name: 'vacations' },
    '/positions': { view: 'positions.php', name: 'positions' },
    '/payroll': { view: 'payroll.php', name: 'payroll' },
    '/subscription': { view: 'subscription.php', name: 'subscription' },
    '/manage_users': { view: 'manage_users.php', name: 'manage_users' },
    '/perfil': { view: 'perfil.php', name: 'perfil' },
    '/help': { view: 'help.php', name: 'help' },
    '/notificacoes': { view: 'notificacoes.php', name: 'notificacoes' },
    '/intelligence': { view: 'intelligence.php', name: 'intelligence' }
  };

  // ═══════════════════════════════════════════════════════════════════════
  // STATE
  // ═══════════════════════════════════════════════════════════════════════

  let currentPath = null;
  let isNavigating = false;
  let abortController = null;

  // ═══════════════════════════════════════════════════════════════════════
  // DOM UTILITIES
  // ═══════════════════════════════════════════════════════════════════════

  function getMainEl() {
    return document.querySelector(config.mainSelector);
  }

  function getLoadingBar() {
    return document.querySelector(config.loadingBarSelector);
  }

  function showLoading() {
    const bar = getLoadingBar();
    if (!bar) return;
    bar.classList.remove('done');
    bar.classList.add('active');
    bar.style.width = '70%';
  }

  function hideLoading() {
    const bar = getLoadingBar();
    if (!bar) return;
    bar.classList.remove('active');
    bar.classList.add('done');
    bar.style.width = '100%';
    setTimeout(() => {
      bar.classList.remove('done');
      bar.style.width = '0%';
    }, 400);
  }

  // ═══════════════════════════════════════════════════════════════════════
  // ROUTE MATCHING
  // ═══════════════════════════════════════════════════════════════════════

  function matchRoute(path) {
    // Exact match first
    if (routes[path]) {
      return routes[path];
    }

    // Try to match path patterns (e.g., /invoices/view?id=5 → /invoices/view)
    const pathWithoutQuery = path.split('?')[0];
    if (routes[pathWithoutQuery]) {
      return routes[pathWithoutQuery];
    }

    // Fallback to dashboard
    return routes['/'];
  }

  // ═══════════════════════════════════════════════════════════════════════
  // LOAD VIEW
  // ═══════════════════════════════════════════════════════════════════════

  async function loadView(path) {
    const route = matchRoute(path);
    const viewPath = route.view;

    showLoading();

    try {
      // Abort previous request if still pending
      if (abortController) {
        abortController.abort();
      }
      abortController = new AbortController();

      const response = await fetch(viewPath, {
        signal: abortController.signal,
        headers: {
          'X-Requested-With': config.xRequestedWith
        }
      });

      if (!response.ok) {
        if (response.status === 401) {
          // Session expired
          showSessionExpired();
          return null;
        }
        throw new Error(`HTTP ${response.status}`);
      }

      const html = await response.text();
      return html;

    } catch (error) {
      if (error.name === 'AbortError') {
        console.log('Previous navigation aborted');
        return null;
      }
      console.error('Error loading view:', error);
      showError(error);
      return null;
    } finally {
      hideLoading();
    }
  }

  // ═══════════════════════════════════════════════════════════════════════
  // RENDER VIEW
  // ═══════════════════════════════════════════════════════════════════════

  function renderView(html) {
    const main = getMainEl();
    if (!main) return;
    main.innerHTML = html;
  }

  // ═══════════════════════════════════════════════════════════════════════
  // EXECUTE SCRIPTS
  // ═══════════════════════════════════════════════════════════════════════

  function executeScripts(container) {
    const scripts = container.querySelectorAll('script');
    
    scripts.forEach((oldScript) => {
      const newScript = document.createElement('script');

      // Copy attributes
      Array.from(oldScript.attributes).forEach((attr) => {
        newScript.setAttribute(attr.name, attr.value);
      });

      // Copy content
      newScript.textContent = oldScript.textContent;

      // Replace
      oldScript.parentNode.replaceChild(newScript, oldScript);
    });
  }

  // ═══════════════════════════════════════════════════════════════════════
  // INITIALIZE PAGE
  // ═══════════════════════════════════════════════════════════════════════

  function initializePage(path) {
    const main = getMainEl();
    if (!main) return;

    // Recreate Lucide icons if available
    if (window.lucide) {
      lucide.createIcons();
    }

    // Execute inline scripts (important for page-specific initialization)
    executeScripts(main);

    // Dispatch custom event for page controllers to hook into
    const event = new CustomEvent('bxpert:page-loaded', {
      detail: { path, route: matchRoute(path) }
    });
    document.dispatchEvent(event);

    // Close mobile menu if open
    if (window.__closeMobileMenu) {
      window.__closeMobileMenu();
    }

    // Update active link in sidebar
    highlightActiveLink();

    // Scroll to top
    window.scrollTo(0, 0);
    main.scrollTo?.(0, 0);
  }

  // ═══════════════════════════════════════════════════════════════════════
  // HIGHLIGHT ACTIVE LINK
  // ═══════════════════════════════════════════════════════════════════════

  function highlightActiveLink() {
    const sidebar = document.querySelector(config.sidebarSelector);
    if (!sidebar) return;

    const links = sidebar.querySelectorAll(config.linkSelector);
    const currentPathname = window.location.pathname;

    links.forEach((link) => {
      const href = link.getAttribute('href');
      const isActive = href === currentPathname || href === currentPath;
      link.classList.toggle('active', isActive);
    });
  }

  // ═══════════════════════════════════════════════════════════════════════
  // ERROR HANDLERS
  // ═══════════════════════════════════════════════════════════════════════

  function showError(error) {
    const main = getMainEl();
    if (!main) return;
    main.innerHTML = `
      <div class="container mt-5">
        <div class="alert alert-danger" role="alert">
          <h4 class="alert-heading">Erro ao carregar página</h4>
          <p>${error?.message || 'Ocorreu um erro desconhecido'}</p>
          <hr>
          <p class="mb-0">
            <button class="btn btn-sm btn-outline-danger" onclick="window.location.reload()">
              Recarregar página
            </button>
          </p>
        </div>
      </div>
    `;
  }

  function showSessionExpired() {
    alert('Sua sessão expirou. Será redirecionado para a página de login.');
    window.location.href = 'login.php';
  }

  // ═══════════════════════════════════════════════════════════════════════
  // NAVIGATE
  // ═══════════════════════════════════════════════════════════════════════

  async function navigate(path, pushState = true) {
    // Prevent duplicate navigation
    if (isNavigating) return;
    if (path === currentPath && !pushState) return;

    isNavigating = true;

    try {
      currentPath = path;

      // Load view
      const html = await loadView(path);
      if (html === null) return; // Error or aborted

      // Render view
      renderView(html);

      // Initialize page
      initializePage(path);

      // Update history
      if (pushState) {
        history.pushState({ path }, '', path);
      }

    } finally {
      isNavigating = false;
    }
  }

  // ═══════════════════════════════════════════════════════════════════════
  // LINK INTERCEPTOR
  // ═══════════════════════════════════════════════════════════════════════

  function bindLinks() {
    document.addEventListener('click', (event) => {
      const link = event.target.closest(config.linkSelector);

      if (!link) return;

      // Ignore if target is _blank
      if (link.target === '_blank') return;

      // Ignore if it's an external link
      if (link.origin && link.origin !== window.location.origin) return;

      // Ignore if download attribute is present
      if (link.hasAttribute('download')) return;

      // Ignore if ctrl/cmd key is pressed
      if (event.ctrlKey || event.metaKey) return;

      event.preventDefault();

      const href = link.getAttribute('href');
      navigate(href);
    });
  }

  // ═══════════════════════════════════════════════════════════════════════
  // HISTORY NAVIGATION (BACK/FORWARD)
  // ═══════════════════════════════════════════════════════════════════════

  function bindHistory() {
    window.addEventListener('popstate', (event) => {
      const path = window.location.pathname;
      // Don't push state again for popstate
      navigate(path, false);
    });
  }

  // ═══════════════════════════════════════════════════════════════════════
  // PUBLIC API
  // ═══════════════════════════════════════════════════════════════════════

  function init() {
    bindLinks();
    bindHistory();
    highlightActiveLink();
    
    // Log initialization
    console.log('BXpertRouter initialized');
  }

  return {
    init,
    navigate,
    highlightActiveLink,
    loadPage: navigate // Alias for compatibility
  };

})();

// ═══════════════════════════════════════════════════════════════════════
// INITIALIZATION ON DOCUMENT READY
// ═══════════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
  BXpertRouter.init();
  BXpertRouter.highlightActiveLink();
});

// Export globally for debugging and external access
window.BXpertRouter = BXpertRouter;
