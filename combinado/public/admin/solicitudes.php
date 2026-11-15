<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';
require __DIR__ . '/../../src/quote.php';

$pdo = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $pdo->prepare('DELETE FROM quote_requests WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
    flash('Solicitud eliminada.');
    redirect('solicitudes.php');
}

$interests = quote_interests();
$rows = $pdo->query('SELECT * FROM quote_requests ORDER BY id DESC LIMIT 300')->fetchAll();
admin_header('Cotizaciones', 'solicitudes');
?>
<div class="a-head"><h1>Solicitudes de cotización</h1></div>
<div class="panel panel-flush">
  <?php if (!$rows): ?><p class="hint pad">Aún no hay solicitudes.</p><?php else: ?>
  <table class="a-table a-table-products">
    <thead><tr><th>Cliente</th><th>Contacto</th><th>Servicio</th><th>Mensaje</th><th><span class="sr-only">Acciones</span></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r):
          $wa = $r['phone'] ? preg_replace('/\D/', '', $r['phone']) : ''; ?>
        <tr>
          <td><b><?= e($r['name']) ?></b><?= $r['company'] ? '<br><small>' . e($r['company']) . '</small>' : '' ?><br><small><?= e($r['created_at']) ?></small></td>
          <td data-label="Contacto"><?php if ($r['phone']): ?><a href="tel:<?= e($r['phone']) ?>"><?= e($r['phone']) ?></a><br><?php endif; ?><?php if ($r['email']): ?><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a><?php endif; ?></td>
          <td data-label="Servicio"><?= e($interests[$r['interest']] ?? $r['interest']) ?></td>
          <td data-label="Mensaje"><?= nl2br(e((string) $r['message'])) ?></td>
          <td class="a-actions">
            <?php if ($wa !== ''): ?><a class="a-btn a-btn-sm" href="https://wa.me/<?= e(str_starts_with($wa, '0') ? '593' . substr($wa, 1) : $wa) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
            <form method="post" data-confirm="¿Eliminar esta solicitud?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="a-btn a-btn-sm a-btn-danger" type="submit">Eliminar</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
