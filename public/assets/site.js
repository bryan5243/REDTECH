(() => {
  // Se marca aquí: si este archivo no carga, todo el contenido sigue visible.
  document.documentElement.classList.add('js');

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const flipTimers = new WeakMap();

  const setFlipText = (element, text) => {
    window.clearTimeout(flipTimers.get(element));
    element.classList.remove('is-flipping', 'is-landing');
    if (reduceMotion.matches || element.textContent === text) {
      element.textContent = text;
      return;
    }
    // Reinicia la animación si el visitante cambia de opción a medio camino.
    void element.offsetWidth;
    element.classList.add('is-flipping');
    flipTimers.set(element, window.setTimeout(() => {
      element.textContent = text;
      element.classList.remove('is-flipping');
      element.classList.add('is-landing');
      element.addEventListener('animationend', () => element.classList.remove('is-landing'), { once: true });
    }, 150));
  };

  const audienceLinks = document.querySelectorAll('.audience [data-para]');
  audienceLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      const para = link.dataset.para;

      document.body.dataset.para = para;
      audienceLinks.forEach((other) => {
        if (other.dataset.para === para) other.setAttribute('aria-current', 'true');
        else other.removeAttribute('aria-current');
      });
      document.querySelectorAll('[data-flip]').forEach((element) => {
        setFlipText(element, element.dataset[para]);
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

  // Sombra de la cabecera al desplazarse.
  const updateScrolled = () => document.documentElement.classList.toggle('is-scrolled', window.scrollY > 8);
  updateScrolled();
  window.addEventListener('scroll', updateScrolled, { passive: true });

  // Aparición al hacer scroll. Sin IntersectionObserver, todo se muestra de inmediato.
  const revealed = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('in');
        revealObserver.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px' });
    revealed.forEach((element) => revealObserver.observe(element));

    // Contadores de cifras editables: siempre terminan en el valor exacto de la base de datos.
    const countObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        countObserver.unobserve(entry.target);
        animateCount(entry.target);
      });
    }, { threshold: 0.6 });
    document.querySelectorAll('[data-count]').forEach((element) => countObserver.observe(element));

    // Si el formulario de la portada está a la vista, "Cotizar" lleva a él en lugar de saltar al final.
    const quick = document.querySelector('.quick');
    const topbarCta = document.querySelector('.topbar-cta');
    let quickVisible = false;
    new IntersectionObserver(([entry]) => { quickVisible = entry.isIntersecting; }, { threshold: 0.6 }).observe(quick);
    topbarCta.addEventListener('click', (event) => {
      if (!quickVisible) return;
      event.preventDefault();
      quick.querySelector('input[name="name"]').focus();
    });
  } else {
    revealed.forEach((element) => element.classList.add('in'));
  }

  function animateCount(element) {
    const finalText = element.textContent.trim();
    const match = finalText.match(/^(\D*)(\d+)(\D*)$/);
    if (!match || reduceMotion.matches) return;
    const [, prefix, digits, suffix] = match;
    const target = Number(digits);
    const duration = 1200;
    const start = performance.now();
    element.setAttribute('aria-label', finalText);
    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - 2 ** (-10 * progress);
      element.textContent = progress === 1 ? finalText : `${prefix}${Math.round(target * eased)}${suffix}`;
      if (progress < 1) window.requestAnimationFrame(tick);
    };
    window.requestAnimationFrame(tick);
  }

  // Cierra el menú móvil al elegir un destino o tocar fuera.
  const navMenu = document.querySelector('.nav-menu');
  if (navMenu) {
    navMenu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => { navMenu.open = false; });
    });
    document.addEventListener('click', (event) => {
      if (navMenu.open && !navMenu.contains(event.target)) navMenu.open = false;
    });
  }

  // Confirma el envío y evita pulsaciones repetidas.
  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]');
      button.classList.add('is-sending');
      button.dataset.label = button.textContent;
      button.textContent = 'Enviando…';
      button.disabled = true;
    });
  });
  window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return;
    document.querySelectorAll('button.is-sending').forEach((button) => {
      button.classList.remove('is-sending');
      button.textContent = button.dataset.label;
      button.disabled = false;
    });
  });

  const message = document.querySelector('[data-focus]');
  // Tras el ancla #contacto, el navegador reubica el foco al cargar; por eso se espera a `load`.
  if (message) window.addEventListener('load', () => window.setTimeout(() => message.focus({ preventScroll: true })));
})();
