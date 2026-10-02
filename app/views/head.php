(function () {
  'use strict';

  function boot() {
    if (window.BXpertRouter && typeof window.BXpertRouter.init === 'function') {
      window.BXpertRouter.init();
    }

    if (window.BXpertRouter && typeof window.BXpertRouter.highlightActiveLink === 'function') {
      window.BXpertRouter.highlightActiveLink();
    }

    if (window.BXpertSPA && typeof window.BXpertSPA.initializeView === 'function') {
      window.BXpertSPA.initializeView(window.location.pathname || '/');
    }
  }

  document.addEventListener('DOMContentLoaded', boot);
})();

























