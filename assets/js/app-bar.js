(function () {
  function emitAction(target, element) {
    const action = element.getAttribute('data-action') || 'noop';

    target.dispatchEvent(
      new CustomEvent('mobikul:app-bar-action', {
        detail: {
          action,
          icon: element.getAttribute('aria-label') || '',
        },
      })
    );
  }

  function bindAppBarActions(target = document) {
    const actionElements = target.querySelectorAll('[data-action]');

    actionElements.forEach((element) => {
      if (element.dataset.mobikulBound === 'true') {
        return;
      }

      element.dataset.mobikulBound = 'true';
      element.addEventListener('click', () => emitAction(target, element));
    });

    const logos = target.querySelectorAll('.mobikul-app-bar-logo[data-placeholder]');

    logos.forEach((logo) => {
      logo.addEventListener('error', () => {
        const placeholder = logo.getAttribute('data-placeholder');

        if (placeholder) {
          logo.setAttribute('src', placeholder);
          return;
        }

        const fallback = document.createElement('span');
        fallback.className = 'mobikul-app-bar-logo-fallback';
        fallback.textContent = 'M';
        logo.replaceWith(fallback);
      });
    });
  }

  window.MobikulNativeAppBar = {
    bind: bindAppBarActions
  };
})();
