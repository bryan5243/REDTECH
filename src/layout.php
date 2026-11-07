<?php

declare(strict_types=1);

function nav_links(): array
{
    return [
        'inicio' => [url('./'), 'Inicio'],
        'servicios' => [url('./#servicios'), 'Servicios'],
        'tienda' => [url('tienda.php'), 'Tienda'],
        'nosotros' => [url('./#nosotros'), 'Nosotros'],
        'contacto' => [url('./#contacto'), 'Contacto'],
    ];
}

function page_header(string $title, string $description, string $active = '', string $bodyClass = ''): void
{
    $wa = whatsapp_number();
    ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <meta name="theme-color" content="#06142b">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:type" content="website">
  <meta name="csrf" content="<?= e(csrf_token()) ?>">
  <link rel="preconnect" href="https://images.unsplash.com">
  <link rel="preload" href="<?= url('assets/fonts/sora-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= url('assets/fonts/inter-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= url('assets/site.css') ?>?v=3">
  <script src="<?= url('assets/site.js') ?>?v=3" defer></script>
</head>
<body class="<?= e($bodyClass) ?>" data-whatsapp="<?= e($wa) ?>" data-order-endpoint="<?= url('pedido.php') ?>">
<a class="skip" href="#principal">Saltar al contenido</a>

<div class="scroll-progress" aria-hidden="true"><span data-progress></span></div>
<header class="topbar" data-topbar>
  <div class="wrap topbar-inner">
    <a class="logo" href="<?= url('./') ?>" aria-label="RedTech, inicio">
      <span class="logo-mark" aria-hidden="true"><i></i><i></i><i></i></span>
      <span class="logo-text">Red<b>Tech</b></span>
    </a>
    <nav class="nav" aria-label="Principal" data-nav>
      <?php foreach (nav_links() as $key => [$href, $label]): ?>
        <a href="<?= e($href) ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a class="btn btn-primary nav-cta-mobile" href="<?= url('./#contacto') ?>">Solicitar cotización</a>
    </nav>
    <div class="topbar-actions">
      <button class="icon-btn cart-btn" type="button" data-cart-open aria-label="Abrir carrito">
        <?= icon('carrito') ?><span class="cart-count" data-cart-count hidden>0</span>
      </button>
      <a class="btn btn-primary topbar-cta" href="<?= url('./#contacto') ?>">Cotizar</a>
      <button class="icon-btn menu-btn" type="button" data-menu aria-expanded="false" aria-label="Abrir menú">
        <?= icon('menu', 'icon icon-open') ?><?= icon('cerrar', 'icon icon-close') ?>
      </button>
    </div>
  </div>
</header>
<?php
}

function page_footer(): void
{
    $site = site_data();
    $categories = categories();
    $pending = '<span class="pending">por confirmar</span>';
    $wa = whatsapp_link('Hola RedTech, quiero más información.');
    $socials = array_filter(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'tiktok' => 'TikTok'], static fn ($k) => setting($k) !== '', ARRAY_FILTER_USE_KEY);
    ?>
<footer class="footer">
  <div class="footer-glow" aria-hidden="true"></div>
  <div class="wrap footer-grid">
    <div class="footer-brand">
      <a class="logo logo-light" href="<?= url('./') ?>">
        <span class="logo-mark" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="logo-text">Red<b>Tech</b></span>
      </a>
      <p>Cableado estructurado, cámaras de seguridad, servidores y equipos de red para empresas y hogares.</p>
      <?php if ($socials): ?>
        <div class="socials">
          <?php foreach ($socials as $key => $label): ?>
            <a href="<?= e(setting($key)) ?>" aria-label="<?= e($label) ?>" rel="noopener" target="_blank"><?= icon($key) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <nav aria-label="Pie de página">
      <h2>Empresa</h2>
      <?php foreach (nav_links() as [$href, $label]): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php endforeach; ?>
    </nav>
    <nav aria-label="Categorías de la tienda">
      <h2>Tienda</h2>
      <?php foreach ($categories as $category): ?>
        <a href="<?= url('tienda.php?categoria=' . rawurlencode($category['slug'])) ?>"><?= e($category['name']) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="footer-contact">
      <h2>Contacto</h2>
      <?php foreach ($site['contacts'] as $contact): ?>
        <p><?= icon($contact['type'] === 'phone' ? 'telefono' : 'correo') ?><span><?= e($contact['value']) ?><?= (int) $contact['confirmed'] === 1 ? '' : ' ' . $pending ?></span></p>
      <?php endforeach; ?>
      <?php $location = $site['settings']['location']; ?>
      <p><?= icon('ubicacion') ?><span><?= e($location['value']) ?><?= (int) $location['confirmed'] === 1 ? '' : ' ' . $pending ?></span></p>
      <?php $schedule = $site['settings']['schedule'] ?? null; if ($schedule && $schedule['value'] !== ''): ?>
        <p><?= icon('reloj') ?><span><?= e($schedule['value']) ?><?= (int) $schedule['confirmed'] === 1 ? '' : ' ' . $pending ?></span></p>
      <?php endif; ?>
    </div>
  </div>
  <div class="wrap footer-legal">
    <p>© <?= date('Y') ?> RedTech. Todos los derechos reservados.</p>
    <p>Fotografías ilustrativas.</p>
  </div>
</footer>

<button class="to-top" type="button" data-totop aria-label="Volver arriba">
  <svg class="to-top-ring" viewBox="0 0 40 40" aria-hidden="true"><circle cx="20" cy="20" r="18" class="ring-track"/><circle cx="20" cy="20" r="18" class="ring-value"/></svg>
  <?= icon('flecha', 'icon to-top-arrow') ?>
</button>

<?php if ($wa !== ''): ?>
  <a class="wa-float" href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp"><?= icon('whatsapp') ?></a>
<?php endif; ?>

<div class="drawer" data-drawer hidden>
  <div class="drawer-backdrop" data-cart-close></div>
  <aside class="drawer-panel" role="dialog" aria-modal="true" aria-labelledby="carrito-titulo" tabindex="-1">
    <header class="drawer-head">
      <h2 id="carrito-titulo"><?= icon('carrito') ?> Tu carrito</h2>
      <button class="icon-btn" type="button" data-cart-close aria-label="Cerrar carrito"><?= icon('cerrar') ?></button>
    </header>
    <div class="drawer-body">
      <div class="cart-empty" data-cart-empty>
        <?= icon('carrito', 'icon cart-empty-icon') ?>
        <p>Tu carrito está vacío.</p>
        <a class="btn btn-primary" href="<?= url('tienda.php') ?>">Ir a la tienda</a>
      </div>
      <ul class="cart-items" data-cart-items></ul>
    </div>
    <form class="drawer-foot" data-checkout hidden>
      <div class="cart-total"><span>Total estimado</span><strong data-cart-total>$0.00</strong></div>
      <p class="cart-note" data-cart-note hidden>Algunos productos tienen precio a consultar: te confirmaremos el valor final.</p>
      <label>Tu nombre <input name="nombre" required maxlength="120" autocomplete="name"></label>
      <label>Ciudad <input name="ciudad" maxlength="120" autocomplete="address-level2"></label>
      <label>Notas (opcional) <textarea name="notas" rows="2" maxlength="500" placeholder="Dirección, instalación, horario de entrega..."></textarea></label>
      <button class="btn btn-whatsapp btn-block" type="submit"><?= icon('whatsapp') ?> Enviar pedido por WhatsApp</button>
      <p class="cart-missing" data-wa-missing hidden>El número de WhatsApp aún no está configurado. Agrégalo en el panel de administración.</p>
    </form>
  </aside>
</div>
<div class="toast" data-toast role="status" aria-live="polite"></div>
</body>
</html>
<?php
}

/** Tarjeta de producto reutilizable. */
function product_card(array $product, int $index = 0): string
{
    ob_start(); ?>
    <article class="pcard reveal" style="--i:<?= $index % 4 ?>">
      <a class="pcard-media" href="<?= url('producto.php?p=' . rawurlencode($product['slug'])) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= e(image_src($product['image'], 600, 480)) ?>" alt="" width="600" height="480" loading="lazy" decoding="async">
        <span class="badge badge-<?= e($product['stock']) ?>"><?= e(stock_label($product['stock'])) ?></span>
      </a>
      <div class="pcard-body">
        <p class="pcard-cat"><?= e($product['category_name']) ?></p>
        <h3><a href="<?= url('producto.php?p=' . rawurlencode($product['slug'])) ?>"><?= e($product['name']) ?></a></h3>
        <p class="pcard-summary"><?= e($product['summary']) ?></p>
        <div class="pcard-foot">
          <span class="price<?= $product['price'] === null ? ' price-ask' : '' ?>"><?= e(money($product['price'])) ?></span>
          <?php if ($product['stock'] !== 'agotado'): ?>
            <button class="btn btn-sm btn-primary" type="button" data-add='<?= cart_payload($product) ?>'><?= icon('carrito') ?><span>Agregar</span></button>
          <?php endif; ?>
        </div>
      </div>
    </article>
    <?php
    return (string) ob_get_clean();
}
