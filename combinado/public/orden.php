<?php

declare(strict_types=1);

// Página pública de un pedido: es el enlace que viaja en el mensaje de WhatsApp.
// Se accede con el código aleatorio del pedido (?c=...), nunca con el número.
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/catalog.php';
require __DIR__ . '/../src/layout.php';

start_session();

$code = (string) ($_GET['c'] ?? '');
$order = null;
$pdo = db();
if ($pdo !== null && preg_match('/^[a-f0-9]{12}$/', $code)) {
    try {
        $statement = $pdo->prepare('SELECT * FROM orders WHERE code = ?');
        $statement->execute([$code]);
        $order = $statement->fetch() ?: null;
    } catch (PDOException $exception) {
        error_log('RedTech DB: ' . $exception->getMessage());
    }
}

if ($order === null) {
    http_response_code(404);
    page_header('Pedido no encontrado | RedTech', 'No encontramos este pedido.', 'tienda', 'page-shop page-product', ['noindex' => true]);
    ?>
    <main id="principal" class="section order-page"><div class="wrap empty-state">
      <?= icon('carrito', 'icon empty-icon') ?>
      <h1>Pedido no encontrado</h1>
      <p>Revisa que el enlace esté completo o escríbenos por WhatsApp.</p>
      <a class="btn btn-primary" href="<?= url('tienda.php') ?>">Ir a la tienda</a>
    </div></main>
    <?php
    page_footer();
    exit;
}

$items = json_decode((string) $order['items'], true) ?: [];
$units = array_sum(array_map(static fn ($i) => (int) $i['qty'], $items));
$hasAsk = (bool) array_filter($items, static fn ($i) => $i['price'] === null);
$number = (int) $order['id'];
$status = ['nuevo' => ['Recibido', 'is-new'], 'atendido' => ['Atendido', 'is-done'], 'cancelado' => ['Cancelado', 'is-cancel']][$order['status']] ?? ['Recibido', 'is-new'];
$date = date('d/m/Y H:i', strtotime((string) $order['created_at']));
$link = absolute_url('orden.php?c=' . $code);
$waLink = whatsapp_link('Hola RedTech, te escribo por mi pedido #' . $number . ': ' . $link);
$firstImage = $items[0]['image'] ?? '';
$summary = implode(', ', array_map(static fn ($i) => $i['qty'] . ' × ' . $i['name'], array_slice($items, 0, 4))) . (count($items) > 4 ? '…' : '');

page_header(
    'Pedido #' . $number . ' · ' . $units . ($units === 1 ? ' producto' : ' productos') . ' | RedTech',
    $summary . ' · Total estimado ' . money((float) $order['total']),
    'tienda',
    'page-shop page-product',
    ['noindex' => true, 'image' => $firstImage !== '' ? absolute_url(image_src($firstImage, 600, 600)) : '']
);
?>
<main id="principal" class="section order-page">
  <div class="wrap order-wrap">
    <header class="order-head reveal">
      <div>
        <p class="order-kicker">Pedido por WhatsApp</p>
        <h1>Pedido #<?= $number ?></h1>
        <p class="order-meta">
          <span class="order-status <?= $status[1] ?>"><?= e($status[0]) ?></span>
          <span><?= e($date) ?></span>
          <span><?= e($order['customer_name']) ?><?= $order['city'] ? ' · ' . e($order['city']) : '' ?></span>
        </p>
      </div>
      <div class="order-actions">
        <?php if ($waLink !== ''): ?>
          <a class="btn btn-whatsapp" href="<?= e($waLink) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> Escribir sobre este pedido</a>
        <?php endif; ?>
        <button class="btn btn-outline" type="button" data-print><?= icon('imprimir') ?> Imprimir</button>
      </div>
    </header>

    <div class="order-grid">
      <ul class="order-items">
        <?php foreach ($items as $index => $item):
            $price = $item['price'] === null ? null : (float) $item['price'];
            $slug = $item['slug'] ?? ''; ?>
          <li class="order-item reveal" style="--i:<?= $index % 6 ?>">
            <img src="<?= e(image_src((string) ($item['image'] ?? ''), 200, 200)) ?>" alt="" width="96" height="96" loading="lazy">
            <div class="order-item-info">
              <h2><?php if ($slug !== ''): ?><a href="<?= url('producto.php?p=' . rawurlencode($slug)) ?>"><?= e($item['name']) ?></a><?php else: ?><?= e($item['name']) ?><?php endif; ?></h2>
              <p><?= (int) $item['qty'] ?> × <span class="<?= $price === null ? 'price-ask' : '' ?>"><?= e(money($price)) ?></span></p>
            </div>
            <strong class="order-item-total<?= $price === null ? ' price-ask' : '' ?>"><?= e($price === null ? 'A consultar' : money($price * (int) $item['qty'])) ?></strong>
          </li>
        <?php endforeach; ?>
      </ul>

      <aside class="order-summary reveal">
        <h2>Resumen</h2>
        <dl>
          <div><dt>Productos</dt><dd><?= $units ?></dd></div>
          <div class="order-total"><dt>Total estimado</dt><dd><?= e(money((float) $order['total'])) ?></dd></div>
        </dl>
        <?php if ($hasAsk): ?><p class="order-note">Incluye productos con precio a consultar: te confirmaremos el valor final.</p><?php endif; ?>
        <?php if ($order['notes']): ?><div class="order-notes"><h3>Notas del cliente</h3><p><?= nl2br(e($order['notes'])) ?></p></div><?php endif; ?>
        <p class="order-help">Precios referenciales. RedTech confirma disponibilidad, valor final y entrega por WhatsApp.</p>
        <a class="btn btn-outline btn-block" href="<?= url('tienda.php') ?>"><?= icon('carrito') ?> Seguir comprando</a>
      </aside>
    </div>
  </div>
</main>
<?php page_footer(); ?>
