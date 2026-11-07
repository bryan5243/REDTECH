<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/catalog.php';
require __DIR__ . '/../src/layout.php';

start_session();

$product = product_by_slug((string) ($_GET['p'] ?? ''));
if ($product === null) {
    http_response_code(404);
    page_header('Producto no encontrado | RedTech', 'El producto que buscas no está disponible.', 'tienda', 'page-shop');
    ?>
    <main id="principal" class="section"><div class="wrap empty-state">
      <?= icon('buscar', 'icon empty-icon') ?>
      <h1>Producto no encontrado</h1>
      <p>Puede que ya no esté disponible. Revisa la tienda o escríbenos.</p>
      <a class="btn btn-primary" href="<?= url('tienda.php') ?>">Ir a la tienda</a>
    </div></main>
    <?php
    page_footer();
    exit;
}

$images = array_values(array_unique(array_filter(array_merge([$product['image']], $product['gallery']))));
$specs = parse_specs($product['specs']);
$related = products(['category' => $product['category_slug'], 'exclude' => $product['id'], 'limit' => 4]);
$waMessage = 'Hola RedTech, me interesa este producto: ' . $product['name'] . ' (' . money($product['price']) . ').';
$waLink = whatsapp_link($waMessage);

page_header($product['name'] . ' | RedTech', $product['summary'], 'tienda', 'page-shop page-product');
?>
<main id="principal">
  <section class="section product-page">
    <div class="wrap">
      <nav class="crumbs crumbs-dark" aria-label="Ruta">
        <a href="<?= url('./') ?>">Inicio</a><span>/</span><a href="<?= url('tienda.php') ?>">Tienda</a><span>/</span>
        <a href="<?= url('tienda.php?categoria=' . rawurlencode($product['category_slug'])) ?>"><?= e($product['category_name']) ?></a><span>/</span><span aria-current="page"><?= e($product['name']) ?></span>
      </nav>

      <div class="pd">
        <div class="pd-gallery" data-gallery>
          <div class="pd-main">
            <img src="<?= e(image_src($images[0] ?? '', 1000, 800)) ?>" alt="<?= e($product['name']) ?>" width="1000" height="800" data-gallery-main fetchpriority="high">
            <span class="badge badge-<?= e($product['stock']) ?>"><?= e(stock_label($product['stock'])) ?></span>
          </div>
          <?php if (count($images) > 1): ?>
            <div class="pd-thumbs">
              <?php foreach ($images as $i => $src): ?>
                <button type="button" data-gallery-thumb="<?= e(image_src($src, 1000, 800)) ?>"<?= $i === 0 ? ' aria-current="true"' : '' ?> aria-label="Ver imagen <?= $i + 1 ?>">
                  <img src="<?= e(image_src($src, 200, 160)) ?>" alt="" width="200" height="160" loading="lazy">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <p class="image-note">Imagen ilustrativa.</p>
        </div>

        <div class="pd-info">
          <p class="pcard-cat"><?= e($product['category_name']) ?><?= $product['brand'] !== '' ? ' · ' . e($product['brand']) : '' ?></p>
          <h1><?= e($product['name']) ?></h1>
          <p class="pd-summary"><?= e($product['summary']) ?></p>
          <p class="pd-price<?= $product['price'] === null ? ' price-ask' : '' ?>"><?= e(money($product['price'])) ?></p>

          <?php if ($product['stock'] !== 'agotado'): ?>
            <div class="pd-buy">
              <div class="qty" data-qty>
                <button type="button" data-qty-minus aria-label="Restar uno"><?= icon('menos') ?></button>
                <input type="number" min="1" max="999" value="1" aria-label="Cantidad" data-qty-input>
                <button type="button" data-qty-plus aria-label="Sumar uno"><?= icon('mas') ?></button>
              </div>
              <button class="btn btn-primary btn-lg" type="button" data-add='<?= cart_payload($product) ?>' data-add-qty><?= icon('carrito') ?> Agregar al carrito</button>
            </div>
          <?php else: ?>
            <p class="alert">Este producto está agotado. Escríbenos y te avisamos cuando vuelva.</p>
          <?php endif; ?>
          <?php if ($waLink !== ''): ?>
            <a class="btn btn-whatsapp btn-lg btn-block" href="<?= e($waLink) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> Pedir por WhatsApp</a>
          <?php endif; ?>

          <ul class="pd-perks">
            <li><?= icon('soporte') ?> Instalación y configuración disponibles</li>
            <li><?= icon('camion') ?> Entrega coordinada contigo</li>
            <li><?= icon('escudo') ?> Asesoría técnica antes de comprar</li>
          </ul>
        </div>
      </div>

      <div class="pd-details">
        <section aria-labelledby="desc-titulo">
          <h2 id="desc-titulo">Descripción</h2>
          <?php foreach (preg_split('/\R/', (string) $product['description']) as $paragraph): if (trim($paragraph) === '') continue; ?>
            <p><?= e($paragraph) ?></p>
          <?php endforeach; ?>
        </section>
        <?php if ($specs !== []): ?>
          <section aria-labelledby="specs-titulo">
            <h2 id="specs-titulo">Especificaciones</h2>
            <table class="specs">
              <?php foreach ($specs as [$key, $value]): ?><tr><th scope="row"><?= e($key) ?></th><td><?= e($value) ?></td></tr><?php endforeach; ?>
            </table>
          </section>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if ($related !== []): ?>
    <section class="section section-tint" aria-labelledby="rel-titulo">
      <div class="wrap">
        <header class="section-head section-head-left reveal"><h2 id="rel-titulo">También te puede interesar</h2></header>
        <div class="products-grid products-grid-4">
          <?php foreach ($related as $i => $item): ?><?= product_card($item, $i) ?><?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php page_footer(); ?>
