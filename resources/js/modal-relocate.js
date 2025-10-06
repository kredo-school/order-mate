/**
 * Move any .modal elements to document.body so Bootstrap backdrops and z-index work reliably.
 * Also watches for future nodes added to the DOM and moves them too.
 *
 * Safe for modals generated inside loops (foreach). Moving preserves event listeners and form inputs.
 */

(function () {
  'use strict';

  function moveModalToBody(modal) {
    if (!modal || modal.parentNode === document.body) return;
    // Preserve display state if Bootstrap already initialized (optional)
    const wasShown = modal.classList.contains('show') || modal.style.display === 'block';

    // Move modal
    document.body.appendChild(modal);

    // Restore display state if needed
    if (wasShown) {
      modal.style.display = 'block';
      // If BS5 modal instance exists we let Bootstrap manage backdrop; otherwise it's fine.
    }
  }

  function moveAllModals() {
    document.querySelectorAll('.modal').forEach(moveModalToBody);
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', moveAllModals);
  } else {
    moveAllModals();
  }

  // Observe for dynamically added modals (e.g. created by JS or included later)
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((m) => {
      m.addedNodes.forEach((node) => {
        if (!(node instanceof HTMLElement)) return;
        if (node.matches && node.matches('.modal')) {
          moveModalToBody(node);
        } else {
          // If subtree contains modals
          node.querySelectorAll && node.querySelectorAll('.modal').forEach(moveModalToBody);
        }
      });
    });
  });

  observer.observe(document.documentElement || document.body, {
    childList: true,
    subtree: true,
  });

  // Optional helper to call manually from console for debugging
  window.relocateModalsToBody = moveAllModals;
})();