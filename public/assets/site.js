(() => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  const flipTimers = new WeakMap();

  const setFlipText = (element, text, index) => {
    window.clearTimeout(flipTimers.get(element));
    element.classList.remove('is-flipping', 'is-landing');
    if (reduceMotion.matches || element.textContent === text) {
      element.textContent = text;
      return;
    }
    // Reinicia la animación si el visitante cambia de opción a medio giro.
    void element.offsetWidth;
    element.style.setProperty('--flip-delay', `${index * 45}ms`);
    element.classList.add('is-flipping');
    flipTimers.set(element, window.setTimeout(() => {
      element.textContent = text;
      element.classList.remove('is-flipping');
      element.classList.add('is-landing');
      element.addEventListener('animationend', () => element.classList.remove('is-landing'), { once: true });
    }, 150 + index * 45));
  };

  document.querySelectorAll('.audience [data-para]').forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      const para = link.dataset.para;

      document.body.dataset.para = para;
      document.querySelectorAll('.audience [data-para]').forEach((other) => {
        if (other === link) other.setAttribute('aria-current', 'true');
        else other.removeAttribute('aria-current');
      });
      document.querySelectorAll('[data-flip]').forEach((element, index) => {
        setFlipText(element, element.dataset[para], index);
      });
      document.querySelectorAll('[data-para-input]').forEach((input) => { input.value = para; });
      document.querySelectorAll('a[href*="para="]:not([data-para])').forEach((other) => {
        other.href = other.href.replace(/para=(empresa|hogar)/, `para=${para}`);
      });

      const url = new URL(window.location.href);
      url.searchParams.set('para', para);
      window.history.replaceState(null, '', url);
    });
  });

  document.querySelectorAll('[data-pick]').forEach((link) => {
    link.addEventListener('click', () => {
      document.querySelectorAll('[data-interest]').forEach((select) => { select.value = link.dataset.pick; });
      const textarea = document.querySelector('#f-message');
      if (link.dataset.product && textarea.value.trim() === '') {
        textarea.value = `Quiero consultar por: ${link.dataset.product}.`;
      }
    });
  });

  const message = document.querySelector('[data-focus]');
  // Tras el ancla #contacto, el navegador reubica el foco al cargar; por eso se espera a `load`.
  if (message) window.addEventListener('load', () => window.setTimeout(() => message.focus({ preventScroll: true })));
})();
