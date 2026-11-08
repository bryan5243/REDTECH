<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $status = (string) ($_POST['status'] ?? '');
    if (in_array($status, ['nuevo', 'atendido', 'cancelado'], true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, (int) ($_POST['id'] ?? 0)]);
        flash('Pedido actualizado.');
    }
    redirect('pedidos.php');
}

$orders = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 200')->fetchAll();
admin_header('Pedidos', 'pedidos');
?>
<div class="a-head"><h1>Pedidos por WhatsApp</h1></div>
<p class="hint">Cada vez que un cliente envía su carrito por WhatsApp, queda registrado aquí. Los precios se toman del catálogo al momento del pedido.</p>
<?php if (!$orders): ?>
  <div class="panel"><p class="hint">Aún no hay pedidos.</p></div>
<?php endif; ?>
<div class="a-orders">
  <?php foreach ($orders as $order): $items = json_decode((string) $order['items'], true) ?: []; ?>
    <article class="panel a-order">
      <header class="a-head">
        <div><h2>#<?= (int) $order['id'] ?> · <?= e($order['customer_name']) ?></h2><small><?= e($order['created_at']) ?><?= $order['city'] ? ' · ' . e($order['city']) : '' ?></small></div>
        <form method="post" class="a-row"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
          <select name="status" aria-label="Estado"><?php foreach (['nuevo' => 'Nuevo', 'atendido' => 'Atendido', 'cancelado' => 'Cancelado'] as $k => $l): ?><option value="<?= $k ?>"<?= $order['status'] === $k ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
          <button class="a-btn a-btn-sm" type="submit">Actualizar</button>
        </form>
      </header>
      <table class="a-table"><tbody>
        <?php foreach ($items as $item): ?>
          <tr><td><?= e($item['name']) ?></td><td>x<?= (int) $item['qty'] ?></td><td><?= e($item['price'] === null ? 'A consultar' : money((float) $item['price'] * (int) $item['qty'])) ?></td></tr>
        <?php endforeach; ?>
        <tr class="a-total"><td colspan="2">Total estimado</td><td><?= e(money((float) $order['total'])) ?></td></tr>
      </tbody></table>
      <?php if ($order['notes']): ?><p class="hint"><b>Notas:</b> <?= e($order['notes']) ?></p><?php endif; ?>
      <?php if (($order['code'] ?? '') !== ''): ?><p><a href="../orden.php?c=<?= e($order['code']) ?>" target="_blank" rel="noopener">Ver la página del pedido que recibió el cliente</a></p><?php endif; ?>
    </article>
  <?php endforeach; ?>
</div>
<?php admin_footer(); ?>
