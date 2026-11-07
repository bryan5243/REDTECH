<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/catalog.php';
require __DIR__ . '/../src/layout.php';

start_session();

$categories = categories();
$current = (string) ($_GET['categoria'] ?? '');
if (!isset($categories[$current])) {
    $current = '';
}
$q = trim(mb_substr((string) ($_GET['q'] ?? ''), 0, 80));
$sorts = ['destacados' => 'Destacados', 'precio-asc' => 'Precio: menor a mayor', 'precio-desc' => 'Precio: mayor a menor', 'nombre' => 'Nombre (A-Z)'];
$sort = (string) ($_GET['orden'] ?? 'destacados');
if (!isset($sorts[$sort])) {
    $sort = 'destacados';
}
$list = products(['category' => $current, 'q' => $q, 'sort' => $sort]);
$total = array_sum(array_map(static fn ($c) => (int) $c['product_count'], $categories));
$heading = $current !== '' ? $categories[$current]['name'] : 'Tienda RedTech';
$lead = $current !== '' ? $categories[$current]['description'] : 'Cables, servidores, routers y cámaras. Arma tu pedido y envíalo por WhatsApp.';

$link = static function (array $changes) use ($current, $q, $sort): string {
    $params = array_filter(array_merge(['categoria' => $current, 'q' => $q, 'orden' => $sort === 'destacados' ? '' : $sort], $changes), static fn ($v) => $v !== '');
    return url('tienda.php' . ($params ? '?' . http_build_query($params) : ''));
};

page_header($heading . ' | RedTech', $lead, 'tienda', 'page-shop');
?>
<main id="principal">
  <section class="page-hero">
    <div class="page-hero-bg" aria-hidden="true"><canvas class="hero-net" data-network></canvas></div>
    <div class="wrap">
      <nav class="crumbs" aria-label="Ruta"><a href="<?= url('./') ?>">Inicio</a><span>/</span><a href="<?= url('tienda.php') ?>">Tienda</a><?php if ($current !== ''): ?><span>/</span><span aria-current="page"><?= e($heading) ?></span><?php endif; ?></nav>
      <h1 class="hero-in" style="--d:0"><?= e($heading) ?></h1>
      <p class="hero-in" style="--d:1"><?= e($lead) ?></p>
      <form class="shop-search hero-in" style="--d:2" action="<?= url('tienda.php') ?>" method="get" role="search">
        <?php if ($current !== ''): ?><input type="hidden" name="categoria" value="<?= e($current) ?>"><?php endif; ?>
        <?= icon('buscar') ?>
        <label class="sr-only" for="buscar">Buscar productos</label>
        <input id="buscar" name="q" type="search" value="<?= e($q) ?>" placeholder="Buscar cable, cámara, router...">
        <button class="btn btn-primary" type="submit">Buscar</button>
      </form>
    </div>
  </section>

  <?php if (setting('catalog_sample') === '1'): ?>
    <div class="wrap"><p class="sample-note">Catálogo de demostración: productos, fotos y precios de ejemplo.</p></div>
  <?php endif; ?>

  <section class="section shop" aria-label="Productos">
    <div class="wrap shop-grid">
      <aside class="filters" aria-label="Categorías">
        <h2>Categorías</h2>
        <ul>
          <li><a href="<?= e($link(['categoria' => ''])) ?>"<?= $current === '' ? ' aria-current="true"' : '' ?>><?= icon('tienda') ?><span>Todos</span><b><?= $total ?></b></a></li>
          <?php foreach ($categories as $slug => $category): ?>
            <li><a href="<?= e($link(['categoria' => $slug])) ?>"<?= $current === $slug ? ' aria-current="true"' : '' ?>><?= icon($slug === 'cables' ? 'cables' : $slug) ?><span><?= e($category['name']) ?></span><b><?= (int) $category['product_count'] ?></b></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="filters-help">
          <?= icon('whatsapp') ?>
          <p><strong>¿Necesitas asesoría?</strong> Te ayudamos a elegir el equipo correcto.</p>
          <?php $help = whatsapp_link('Hola RedTech, necesito asesoría para elegir un equipo.'); if ($help !== ''): ?><a class="link-arrow" href="<?= e($help) ?>" target="_blank" rel="noopener">Escríbenos <?= icon('flecha') ?></a><?php else: ?><a class="link-arrow" href="<?= url('./#contacto') ?>">Contáctanos <?= icon('flecha') ?></a><?php endif; ?>
        </div>
      </aside>

      <div class="shop-main">
        <div class="shop-toolbar">
          <p><strong><?= count($list) ?></strong> <?= count($list) === 1 ? 'producto' : 'productos' ?><?= $q !== '' ? ' para «' . e($q) . '»' : '' ?></p>
          <form method="get" action="<?= url('tienda.php') ?>" data-autosubmit>
            <?php if ($current !== ''): ?><input type="hidden" name="categoria" value="<?= e($current) ?>"><?php endif; ?>
            <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
            <label for="orden">Ordenar</label>
            <select id="orden" name="orden">
              <?php foreach ($sorts as $value => $label): ?><option value="<?= e($value) ?>"<?= $sort === $value ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
            <noscript><button class="btn btn-sm btn-outline" type="submit">Aplicar</button></noscript>
          </form>
        </div>

        <?php if ($list === []): ?>
          <div class="empty-state">
            <?= icon('buscar', 'icon empty-icon') ?>
            <h2>No encontramos productos</h2>
            <p>Prueba con otra búsqueda o escríbenos: conseguimos el equipo que necesitas.</p>
            <a class="btn btn-primary" href="<?= url('tienda.php') ?>">Ver todos los productos</a>
          </div>
        <?php else: ?>
          <div class="products-grid">
            <?php foreach ($list as $i => $product): ?><?= product_card($product, $i) ?><?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="perks" aria-label="Ventajas de comprar en RedTech">
    <div class="wrap perks-grid">
      <div class="perk reveal"><?= icon('whatsapp') ?><div><h3>Pedido por WhatsApp</h3><p>Envía tu carrito y coordinamos contigo.</p></div></div>
      <div class="perk reveal" style="--i:1"><?= icon('soporte') ?><div><h3>Instalación opcional</h3><p>Te lo dejamos funcionando.</p></div></div>
      <div class="perk reveal" style="--i:2"><?= icon('camion') ?><div><h3>Entrega coordinada</h3><p>Acordamos la entrega contigo.</p></div></div>
      <div class="perk reveal" style="--i:3"><?= icon('escudo') ?><div><h3>Asesoría técnica</h3><p>Te ayudamos a elegir bien.</p></div></div>
    </div>
  </section>
</main>
<?php page_footer(); ?>
