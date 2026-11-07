/* RedTech: interacciones del sitio y carrito por WhatsApp */
(() => {
  'use strict';
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const body = document.body;

  /* Barra superior: se vuelve sólida al bajar */
  const topbar = $('[data-topbar]');
  const forceSolid = body.classList.contains('page-product');
  const onScroll = () => topbar && topbar.classList.toggle('is-solid', forceSolid || scrollY > 40);
  onScroll();
  addEventListener('scroll', onScroll, { passive: true });

  /* Menú móvil */
  const menuBtn = $('[data-menu]');
  const nav = $('[data-nav]');
  if (menuBtn && nav) {
    const setMenu = (open) => {
      menuBtn.setAttribute('aria-expanded', String(open));
      menuBtn.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
      nav.classList.toggle('is-open', open);
      if (open) topbar.classList.add('is-solid'); else onScroll();
    };
    menuBtn.addEventListener('click', () => setMenu(menuBtn.getAttribute('aria-expanded') !== 'true'));
    nav.addEventListener('click', (e) => { if (e.target.closest('a')) setMenu(false); });
    addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
  }

  /* Aparición al hacer scroll */
  const revealables = $$('.reveal, [data-steps]');
  if ('IntersectionObserver' in window && !reduced) {
    // Se anima al entrar y se reinicia al salir, así hay movimiento al bajar y al subir.
    // Si el elemento entra por arriba (al subir), aparece desde arriba.
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        const el = entry.target;
        const fromTop = entry.boundingClientRect.top < (entry.rootBounds ? entry.rootBounds.top : 0) + 10;
        if (entry.isIntersecting) {
          el.classList.toggle('from-top', fromTop);
          el.classList.add('is-visible');
        } else {
          el.classList.remove('is-visible');
          el.classList.toggle('from-top', entry.boundingClientRect.top < 0);
        }
      });
    }, { rootMargin: '0px 0px -6% 0px', threshold: .1 });
    revealables.forEach((el) => io.observe(el));
  } else {
    revealables.forEach((el) => el.classList.add('is-visible'));
  }

  /* Barra de progreso, botón para subir y parallax */
  const progressBar = $('[data-progress]');
  const toTop = $('[data-totop]');
  const ring = toTop ? $('circle.ring-value', toTop) : null;
  const parallax = reduced ? [] : $$('[data-parallax]');
  const heroCopy = reduced ? null : $('.hero-copy');
  let ticking = false;
  const onScrollFx = () => {
    ticking = false;
    const max = document.documentElement.scrollHeight - innerHeight;
    const ratio = max > 0 ? Math.min(1, scrollY / max) : 0;
    if (progressBar) progressBar.style.transform = `scaleX(${ratio})`;
    if (toTop) {
      toTop.classList.toggle('is-on', scrollY > 600);
      if (ring) ring.style.strokeDashoffset = String(113.1 * (1 - ratio));
    }
    parallax.forEach((el) => {
      const r = el.getBoundingClientRect();
      if (r.bottom < -200 || r.top > innerHeight + 200) return;
      const speed = parseFloat(el.dataset.parallax) || .15;
      const offset = (r.top + r.height / 2 - innerHeight / 2) * -speed;
      el.style.setProperty('--py', offset.toFixed(1) + 'px');
    });
    if (heroCopy && scrollY < innerHeight) {
      const k = scrollY / innerHeight;
      heroCopy.style.transform = `translateY(${k * 90}px)`;
      heroCopy.style.opacity = String(1 - k * 1.1);
    }
  };
  addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(onScrollFx); } }, { passive: true });
  addEventListener('resize', onScrollFx);
  onScrollFx();
  toTop?.addEventListener('click', () => {
    scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
    $('#principal')?.focus({ preventScroll: true });
  });

  /* Contadores animados: "+150" o "100%"; los valores sin número se muestran tal cual */
  const counters = $$('[data-count]');
  const animateCount = (el) => {
    const raw = el.dataset.count;
    const m = raw.match(/^(\D*)(\d+(?:[.,]\d+)?)(\D*)$/);
    if (!m || reduced) return;
    const [, pre, num, post] = m;
    const target = parseFloat(num.replace(',', '.'));
    const start = performance.now();
    const dur = 1600;
    const tick = (now) => {
      const p = Math.min(1, (now - start) / dur);
      const eased = 1 - Math.pow(1 - p, 4);
      el.textContent = pre + Math.round(target * eased) + post;
      if (p < 1) requestAnimationFrame(tick); else el.textContent = raw;
    };
    requestAnimationFrame(tick);
  };
  if ('IntersectionObserver' in window) {
    const cio = new IntersectionObserver((entries) => entries.forEach((en) => {
      if (en.isIntersecting) animateCount(en.target);
    }), { threshold: .6 });
    counters.forEach((c) => cio.observe(c));
  }

  /* Palabra rotativa del título */
  const rotator = $('[data-rotator]');
  if (rotator && !reduced) {
    const words = JSON.parse(rotator.dataset.rotator || '[]');
    let index = 0;
    if (words.length > 1) {
      setInterval(() => {
        const current = $('.rotator-word', rotator);
        current.classList.add('out');
        index = (index + 1) % words.length;
        setTimeout(() => {
          current.textContent = words[index];
          current.classList.remove('out');
          current.classList.remove('in');
          void current.offsetWidth;
          current.classList.add('in');
        }, 420);
      }, 2800);
    }
  }

  /* Red animada en el fondo (lienzo ligero) */
  $$('[data-network]').forEach((canvas) => {
    if (reduced) return;
    const ctx = canvas.getContext('2d');
    let w, h, dpr, points = [], raf, visible = true;
    const mouse = { x: -999, y: -999 };
    const resize = () => {
      dpr = Math.min(devicePixelRatio || 1, 2);
      w = canvas.clientWidth; h = canvas.clientHeight;
      canvas.width = w * dpr; canvas.height = h * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      const count = Math.round(Math.min(90, (w * h) / 16000));
      points = Array.from({ length: count }, () => ({
        x: Math.random() * w, y: Math.random() * h,
        vx: (Math.random() - .5) * .35, vy: (Math.random() - .5) * .35,
      }));
    };
    const step = () => {
      ctx.clearRect(0, 0, w, h);
      const max = 140;
      for (let i = 0; i < points.length; i++) {
        const p = points[i];
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0 || p.x > w) p.vx *= -1;
        if (p.y < 0 || p.y > h) p.vy *= -1;
        for (let j = i + 1; j < points.length; j++) {
          const q = points[j];
          const d = Math.hypot(p.x - q.x, p.y - q.y);
          if (d < max) {
            ctx.strokeStyle = `rgba(90,170,255,${(1 - d / max) * .35})`;
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
          }
        }
        const dm = Math.hypot(p.x - mouse.x, p.y - mouse.y);
        if (dm < 180) {
          ctx.strokeStyle = `rgba(25,198,232,${(1 - dm / 180) * .6})`;
          ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(mouse.x, mouse.y); ctx.stroke();
        }
        ctx.fillStyle = 'rgba(150,215,255,.85)';
        ctx.beginPath(); ctx.arc(p.x, p.y, 1.6, 0, Math.PI * 2); ctx.fill();
      }
      if (visible) raf = requestAnimationFrame(step);
    };
    resize();
    addEventListener('resize', () => { cancelAnimationFrame(raf); resize(); if (visible) step(); });
    const host = canvas.closest('section') || canvas.parentElement;
    host.addEventListener('pointermove', (e) => { const r = canvas.getBoundingClientRect(); mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; });
    host.addEventListener('pointerleave', () => { mouse.x = mouse.y = -999; });
    new IntersectionObserver(([en]) => {
      visible = en.isIntersecting;
      cancelAnimationFrame(raf);
      if (visible) step();
    }).observe(canvas);
  });

  /* Inclinación suave de la tarjeta del hero */
  const tilt = $('[data-tilt]');
  if (tilt && !reduced && matchMedia('(hover: hover)').matches) {
    const hero = tilt.closest('.hero');
    hero.addEventListener('pointermove', (e) => {
      const r = hero.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - .5;
      const y = (e.clientY - r.top) / r.height - .5;
      tilt.style.transform = `perspective(1000px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg)`;
    });
    hero.addEventListener('pointerleave', () => { tilt.style.transform = ''; });
  }

  /* Carrusel de destacados */
  const carousel = $('[data-carousel]');
  if (carousel) {
    const slideBy = (dir) => {
      const slide = $('.carousel-slide', carousel);
      if (!slide) return;
      const gap = parseFloat(getComputedStyle(carousel).columnGap) || 20;
      const amount = (slide.offsetWidth + gap) * dir;
      const atEnd = carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 8;
      if (dir > 0 && atEnd) carousel.scrollTo({ left: 0 });
      else carousel.scrollBy({ left: amount });
    };
    $('[data-carousel-prev]')?.addEventListener('click', () => slideBy(-1));
    $('[data-carousel-next]')?.addEventListener('click', () => slideBy(1));
    carousel.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') { e.preventDefault(); slideBy(1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); slideBy(-1); }
    });
    if (!reduced) {
      let paused = false;
      ['pointerenter', 'focusin', 'touchstart'].forEach((ev) => carousel.addEventListener(ev, () => { paused = true; }, { passive: true }));
      ['pointerleave', 'focusout'].forEach((ev) => carousel.addEventListener(ev, () => { paused = false; }));
      setInterval(() => { if (!paused && !document.hidden) slideBy(1); }, 4500);
    }
  }

  /* Formulario: preseleccionar el servicio desde "Cotizar" */
  $$('[data-pick]').forEach((link) => link.addEventListener('click', () => {
    const select = $('[data-interest]');
    if (select) select.value = link.dataset.pick;
  }));
  $('[data-focus]')?.focus();

  /* Orden de la tienda */
  $$('[data-autosubmit] select').forEach((s) => s.addEventListener('change', () => s.form.submit()));

  /* Galería de producto */
  const mainImg = $('[data-gallery-main]');
  $$('[data-gallery-thumb]').forEach((btn) => btn.addEventListener('click', () => {
    $$('[data-gallery-thumb]').forEach((b) => b.removeAttribute('aria-current'));
    btn.setAttribute('aria-current', 'true');
    mainImg.classList.add('is-swapping');
    setTimeout(() => { mainImg.src = btn.dataset.galleryThumb; mainImg.onload = () => mainImg.classList.remove('is-swapping'); }, 180);
  }));

  /* Selector de cantidad */
  const clampQty = (v) => Math.max(1, Math.min(999, parseInt(v, 10) || 1));
  $$('[data-qty]').forEach((box) => {
    const input = $('[data-qty-input]', box);
    $('[data-qty-minus]', box).addEventListener('click', () => { input.value = clampQty(input.value - 1); });
    $('[data-qty-plus]', box).addEventListener('click', () => { input.value = clampQty(+input.value + 1); });
    input.addEventListener('change', () => { input.value = clampQty(input.value); });
  });

  /* ---------- Carrito ---------- */
  const KEY = 'redtech_cart_v1';
  const money = (n) => '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const load = () => { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch { return []; } };
  const save = (items) => { try { localStorage.setItem(KEY, JSON.stringify(items)); } catch { /* sin almacenamiento */ } };
  let cart = load();

  const drawer = $('[data-drawer]');
  const list = $('[data-cart-items]');
  const empty = $('[data-cart-empty]');
  const checkout = $('[data-checkout]');
  const totalEl = $('[data-cart-total]');
  const noteEl = $('[data-cart-note]');
  const countEl = $('[data-cart-count]');
  const waMissing = $('[data-wa-missing]');
  const waNumber = body.dataset.whatsapp || '';
  const toastEl = $('[data-toast]');
  let toastTimer;

  const toast = (msg) => {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add('is-on');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('is-on'), 2600);
  };

  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  const render = () => {
    const count = cart.reduce((a, i) => a + i.qty, 0);
    if (countEl) {
      countEl.hidden = count === 0;
      countEl.textContent = count;
    }
    if (!list) return;
    empty.hidden = cart.length > 0;
    checkout.hidden = cart.length === 0;
    list.innerHTML = cart.map((item, idx) => `
      <li class="cart-item">
        <img src="${esc(item.image)}" alt="" width="68" height="68" loading="lazy">
        <div>
          <h3>${esc(item.name)}</h3>
          <span class="price${item.price === null ? ' price-ask' : ''}">${item.price === null ? 'Precio a consultar' : money(item.price * item.qty)}</span>
          <div class="qty">
            <button type="button" data-dec="${idx}" aria-label="Restar uno"><svg class="icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M10 24h28"/></svg></button>
            <input type="number" min="1" max="999" value="${item.qty}" data-set="${idx}" aria-label="Cantidad de ${esc(item.name)}">
            <button type="button" data-inc="${idx}" aria-label="Sumar uno"><svg class="icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M24 10v28M10 24h28"/></svg></button>
          </div>
        </div>
        <button class="cart-remove" type="button" data-remove="${idx}" aria-label="Quitar ${esc(item.name)}"><svg class="icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M8 13h32M19 13V7h10v6M12 13l2 28h20l2-28"/></svg></button>
      </li>`).join('');
    const total = cart.reduce((a, i) => a + (i.price || 0) * i.qty, 0);
    totalEl.textContent = money(total);
    noteEl.hidden = !cart.some((i) => i.price === null);
    if (waMissing) waMissing.hidden = waNumber !== '';
    const submit = $('button[type=submit]', checkout);
    if (submit) submit.disabled = waNumber === '';
  };

  const update = (next) => { cart = next; save(cart); render(); };

  let lastFocus = null;
  const openDrawer = () => {
    if (!drawer) return;
    lastFocus = document.activeElement;
    drawer.hidden = false;
    body.classList.add('drawer-open');
    requestAnimationFrame(() => { drawer.classList.add('is-open'); $('.drawer-panel', drawer).focus(); });
  };
  const closeDrawer = () => {
    if (!drawer || drawer.hidden) return;
    drawer.classList.remove('is-open');
    body.classList.remove('drawer-open');
    setTimeout(() => { drawer.hidden = true; }, reduced ? 0 : 380);
    lastFocus?.focus();
  };
  $$('[data-cart-open]').forEach((b) => b.addEventListener('click', openDrawer));
  $$('[data-cart-close]').forEach((b) => b.addEventListener('click', closeDrawer));
  addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-add]');
    if (add) {
      let product;
      try { product = JSON.parse(add.dataset.add); } catch { return; }
      const qtyInput = add.hasAttribute('data-add-qty') ? $('[data-qty-input]') : null;
      const qty = qtyInput ? clampQty(qtyInput.value) : 1;
      const next = [...cart];
      const found = next.find((i) => i.id === product.id);
      if (found) found.qty = clampQty(found.qty + qty);
      else next.push({ ...product, qty });
      update(next);
      add.classList.add('is-added');
      setTimeout(() => add.classList.remove('is-added'), 1200);
      if (countEl) { countEl.classList.remove('bump'); void countEl.offsetWidth; countEl.classList.add('bump'); }
      toast(`Agregado: ${product.name}`);
      return;
    }
    const inc = e.target.closest('[data-inc]');
    const dec = e.target.closest('[data-dec]');
    const rem = e.target.closest('[data-remove]');
    if (inc || dec) {
      const idx = +(inc || dec).dataset[inc ? 'inc' : 'dec'];
      const next = [...cart];
      next[idx] = { ...next[idx], qty: clampQty(next[idx].qty + (inc ? 1 : -1)) };
      update(next);
    }
    if (rem) update(cart.filter((_, i) => i !== +rem.dataset.remove));
  });
  list?.addEventListener('change', (e) => {
    const input = e.target.closest('[data-set]');
    if (!input) return;
    const next = [...cart];
    next[+input.dataset.set] = { ...next[+input.dataset.set], qty: clampQty(input.value) };
    update(next);
  });

  checkout?.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!waNumber || cart.length === 0) return;
    const data = new FormData(checkout);
    const name = String(data.get('nombre') || '').trim();
    if (!name) { checkout.elements.nombre.focus(); return; }
    const city = String(data.get('ciudad') || '').trim();
    const notes = String(data.get('notas') || '').trim();
    const total = cart.reduce((a, i) => a + (i.price || 0) * i.qty, 0);
    const lines = cart.map((i, n) => `${n + 1}. ${i.name} x${i.qty} - ${i.price === null ? 'precio a consultar' : money(i.price * i.qty)}`);
    const text = [
      'Hola RedTech, quiero hacer este pedido:',
      '',
      ...lines,
      '',
      `*Total estimado: ${money(total)}*`,
      cart.some((i) => i.price === null) ? '(Incluye productos con precio a consultar)' : '',
      '',
      `Nombre: ${name}`,
      city ? `Ciudad: ${city}` : '',
      notes ? `Notas: ${notes}` : '',
    ].join('\n').replace(/\n{3,}/g, '\n\n').trim();

    const endpoint = body.dataset.orderEndpoint;
    const csrf = $('meta[name=csrf]')?.content;
    if (endpoint) {
      try {
        fetch(endpoint, {
          method: 'POST', keepalive: true, headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ csrf, nombre: name, ciudad: city, notas: notes, items: cart.map((i) => ({ id: i.id, qty: i.qty })) }),
        }).catch(() => {});
      } catch { /* el pedido sigue por WhatsApp */ }
    }
    window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
    update([]);
    checkout.reset();
    closeDrawer();
    toast('Abrimos WhatsApp con tu pedido');
  });

  addEventListener('storage', (e) => { if (e.key === KEY) { cart = load(); render(); } });
  render();
})();
