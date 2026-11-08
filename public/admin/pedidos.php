<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();

// Pestañas: cada pedido avanza de Nuevos → Atendidos → Ventas realizadas o No concretados.
$views = [
    'nuevos' => ['Nuevos', ['nuevo'], 'Pedidos recién llegados por WhatsApp. Márcalos como atendidos cuando le respondas al cliente.'],
    'atendidos' => ['Atendidos', ['atendido'], 'Ya le respondiste al cliente. Cierra cada pedido: sube la factura si se vendió, o márcalo como no concretado.'],
    'ventas' => ['Ventas realizadas', ['vendido'], 'Pedidos que terminaron en venta, con su factura.'],
    'no' => ['No concretados', ['no_concretado', 'cancelado'], 'Pedidos que no terminaron en venta.'],
];
$view = isset($views[$_GET['vista'] ?? '']) ? $_GET['vista'] : 'nuevos';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $back = (string) ($_POST['vista'] ?? 'nuevos');
    $statement = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
    $statement->execute([$id]);
    $order = $statement->fetch();

    try {
        if (!$order) {
            flash('Ese pedido no existe.', 'error');
        } elseif ($action === 'atender') {
            $pdo->prepare("UPDATE orders SET status = 'atendido' WHERE id = ?")->execute([$id]);
            flash('Pedido #' . $id . ' pasó a Atendidos.');
            $back = 'atendidos';
        } elseif ($action === 'vendido') {
            $invoice = save_invoice('factura');
            $pdo->prepare("UPDATE orders SET status = 'vendido', invoice = COALESCE(?, invoice), close_note = NULL, closed_at = NOW() WHERE id = ?")->execute([$invoice, $id]);
            $updated = 0;
            if (isset($_POST['descontar'])) {
                foreach (json_decode((string) $order['items'], true) ?: [] as $item) {
                    $updated += discount_stock($pdo, (int) ($item['id'] ?? 0), (int) ($item['qty'] ?? 0)) ? 1 : 0;
                }
            }
            flash('Venta del pedido #' . $id . ' registrada' . ($invoice ? ' con su factura' : ' (sin factura)') . ($updated ? ". Stock descontado en $updated " . ($updated === 1 ? 'producto' : 'productos') : '') . '.');
            $back = 'ventas';
        } elseif ($action === 'factura') {
            $invoice = save_invoice('factura');
            if ($invoice === null) {
                flash('Elige el archivo de la factura.', 'error');
            } else {
                $pdo->prepare('UPDATE orders SET invoice = ? WHERE id = ?')->execute([$invoice, $id]);
                flash('Factura del pedido #' . $id . ' guardada.');
            }
        } elseif ($action === 'no_concretado') {
            $note = trim(mb_substr((string) ($_POST['motivo'] ?? ''), 0, 255));
            $pdo->prepare("UPDATE orders SET status = 'no_concretado', close_note = ?, closed_at = NOW() WHERE id = ?")->execute([$note !== '' ? $note : null, $id]);
            flash('Pedido #' . $id . ' marcado como no concretado.');
            $back = 'no';
        } elseif ($action === 'reabrir' && $order['status'] !== 'vendido') {
            $pdo->prepare("UPDATE orders SET status = 'atendido', close_note = NULL, closed_at = NULL WHERE id = ?")->execute([$id]);
            flash('Pedido #' . $id . ' volvió a Atendidos.');
            $back = 'atendidos';
        }
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
    }
    redirect('pedidos.php?vista=' . rawurlencode(isset($views[$back]) ? $back : 'nuevos'));
}

$counts = array_fill_keys(array_keys($views), 0);
foreach ($pdo->query('SELECT status, COUNT(*) AS n FROM orders GROUP BY status') as $row) {
    foreach ($views as $key => [, $statuses]) {
        if (in_array($row['status'], $statuses, true)) {
            $counts[$key] += (int) $row['n'];
        }
    }
}
$in = implode(',', array_fill(0, count($views[$view][1]), '?'));
$statement = $pdo->prepare("SELECT * FROM orders WHERE status IN ($in) ORDER BY " . ($view === 'nuevos' || $view === 'atendidos' ? 'id DESC' : 'closed_at DESC, id DESC') . ' LIMIT 200');
$statement->execute($views[$view][1]);
$orders = $statement->fetchAll();
$salesTotal = $view === 'ventas' ? (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = 'vendido'")->fetchColumn() : 0.0;
$statuses = order_statuses();
$hidden = static fn (array $order, string $action): string => csrf_field() . '<input type="hidden" name="id" value="' . (int) $order['id'] . '"><input type="hidden" name="action" value="' . $action . '"><input type="hidden" name="vista" value="' . e($GLOBALS['view']) . '">';

admin_header('Pedidos', 'pedidos');
?>
<div class="a-head"><h1>Pedidos por WhatsApp</h1></div>
<nav class="a-tabs" aria-label="Estado de los pedidos">
  <?php foreach ($views as $key => [$label]): ?>
    <a href="?vista=<?= $key ?>"<?= $key === $view ? ' aria-current="page"' : '' ?>><?= e($label) ?> <span><?= $counts[$key] ?></span></a>
  <?php endforeach; ?>
</nav>
<p class="hint"><?= e($views[$view][2]) ?><?php if ($view === 'ventas' && $counts['ventas'] > 0): ?> Total vendido: <b><?= e(money($salesTotal)) ?></b>.<?php endif; ?></p>

<?php if (!$orders): ?>
  <div class="panel"><p class="hint">No hay pedidos en esta sección.</p></div>
<?php endif; ?>
<div class="a-orders">
  <?php foreach ($orders as $order): $items = json_decode((string) $order['items'], true) ?: []; [$statusLabel, $statusClass] = $statuses[$order['status']] ?? ['', '']; ?>
    <article class="panel a-order" id="pedido-<?= (int) $order['id'] ?>">
      <header class="a-head">
        <div>
          <h2>#<?= (int) $order['id'] ?> · <?= e($order['customer_name']) ?> <span class="tag <?= $statusClass ?>"><?= e($statusLabel) ?></span></h2>
          <small><?= e(date('d/m/Y H:i', strtotime((string) $order['created_at']))) ?><?= $order['city'] ? ' · ' . e($order['city']) : '' ?><?php if ($order['closed_at']): ?> · cerrado el <?= e(date('d/m/Y', strtotime((string) $order['closed_at']))) ?><?php endif; ?></small>
        </div>
        <?php if (($order['code'] ?? '') !== ''): ?><a class="a-btn a-btn-sm" href="../orden.php?c=<?= e($order['code']) ?>" target="_blank" rel="noopener">Ver página del pedido</a><?php endif; ?>
      </header>
      <table class="a-table"><tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= e($item['name']) ?></td>
            <td>x<?= (int) $item['qty'] ?></td>
            <td><?= e($item['price'] === null ? 'A consultar' : money((float) $item['price'] * (int) $item['qty'])) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="a-total"><td colspan="2">Total estimado</td><td><?= e(money((float) $order['total'])) ?></td></tr>
      </tbody></table>
      <?php if ($order['notes']): ?><p class="hint"><b>Notas del cliente:</b> <?= e($order['notes']) ?></p><?php endif; ?>
      <?php if ($order['close_note']): ?><p class="hint"><b>Motivo:</b> <?= e($order['close_note']) ?></p><?php endif; ?>

      <?php if ($order['status'] === 'nuevo'): ?>
        <div class="a-row a-order-actions">
          <form method="post"><?= $hidden($order, 'atender') ?><button class="a-btn a-btn-primary a-btn-sm" type="submit">Marcar como atendido</button></form>
          <form method="post" class="a-row"><?= $hidden($order, 'no_concretado') ?><input name="motivo" maxlength="255" placeholder="Motivo (opcional)" aria-label="Motivo"><button class="a-btn a-btn-sm" type="submit">No se concretó</button></form>
        </div>
      <?php elseif ($order['status'] === 'atendido'): ?>
        <div class="a-close">
          <form method="post" enctype="multipart/form-data" class="a-close-sale">
            <?= $hidden($order, 'vendido') ?>
            <h3>¿Se realizó la venta?</h3>
            <label>Factura (PDF o foto) <input type="file" name="factura" accept="application/pdf,image/jpeg,image/png,image/webp"></label>
            <label class="a-check"><input type="checkbox" name="descontar" checked> Descontar estos productos del stock</label>
            <button class="a-btn a-btn-primary a-btn-sm" type="submit">Venta realizada</button>
          </form>
          <form method="post" class="a-close-lost">
            <?= $hidden($order, 'no_concretado') ?>
            <h3>¿No se vendió?</h3>
            <label>Motivo (opcional) <input name="motivo" maxlength="255" placeholder="Ej.: el cliente no respondió"></label>
            <button class="a-btn a-btn-sm" type="submit">No se concretó</button>
          </form>
        </div>
      <?php elseif ($order['status'] === 'vendido'): ?>
        <div class="a-row a-order-actions">
          <?php if ($order['invoice']): ?>
            <a class="a-btn a-btn-sm a-btn-primary" href="factura.php?id=<?= (int) $order['id'] ?>" target="_blank" rel="noopener"><?= icon('check') ?> Ver factura</a>
          <?php else: ?>
            <span class="tag tag-bajo_pedido">Sin factura</span>
          <?php endif; ?>
          <form method="post" enctype="multipart/form-data" class="a-row"><?= $hidden($order, 'factura') ?><input type="file" name="factura" required accept="application/pdf,image/jpeg,image/png,image/webp" aria-label="Archivo de la factura"><button class="a-btn a-btn-sm" type="submit"><?= $order['invoice'] ? 'Reemplazar factura' : 'Subir factura' ?></button></form>
        </div>
      <?php else: ?>
        <div class="a-row a-order-actions">
          <form method="post"><?= $hidden($order, 'reabrir') ?><button class="a-btn a-btn-sm" type="submit">Reabrir pedido</button></form>
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
</div>
<?php admin_footer(); ?>
