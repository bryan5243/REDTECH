<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';
require __DIR__ . '/../../src/backup.php';

$pdo = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    @set_time_limit(300);
    $stamp = date('Y-m-d_H-i');
    $tmp = tempnam(sys_get_temp_dir(), 'rt-backup-');
    try {
        if (($_POST['tipo'] ?? '') === 'base') {
            backup_database($pdo, $tmp);
            backup_send($tmp, "redtech-base-de-datos_$stamp.sql", 'application/sql; charset=utf-8');
        }
        backup_full($pdo, $tmp, isset($_POST['con_config']));
        backup_send($tmp, "redtech-respaldo-completo_$stamp.zip", 'application/zip');
    } catch (Throwable $exception) {
        @unlink($tmp);
        @unlink($tmp . '.sql');
        error_log('RedTech respaldo: ' . $exception->getMessage());
        flash($exception instanceof RuntimeException ? $exception->getMessage() : 'No se pudo generar el respaldo. Intenta de nuevo o descarga solo la base de datos.', 'error');
        redirect('respaldo.php');
    }
}

$stats = [];
foreach (['products' => 'productos', 'orders' => 'pedidos', 'quote_requests' => 'cotizaciones'] as $table => $label) {
    $stats[$label] = (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}
$zipOk = class_exists(ZipArchive::class);

admin_header('Respaldo', 'respaldo');
?>
<div class="a-head"><h1>Respaldo</h1></div>
<p class="hint">Descarga una copia de seguridad cuando quieras: se genera en el momento y no queda guardada en el servidor. Te recomendamos sacarla una vez por semana y guardarla en tu computadora o en Google Drive.</p>

<div class="a-backup">
  <form class="panel a-backup-card" method="post">
    <?= csrf_field() ?><input type="hidden" name="tipo" value="completo">
    <span class="a-backup-icon" aria-hidden="true"><?= icon('servidores') ?></span>
    <h2>Respaldo completo</h2>
    <p>Un archivo ZIP con todo para reinstalar el sitio:</p>
    <ul>
      <li>La base de datos (<?= $stats['productos'] ?> productos, <?= $stats['pedidos'] ?> pedidos, <?= $stats['cotizaciones'] ?> cotizaciones y los ajustes).</li>
      <li>Las fotos subidas y las facturas.</li>
      <li>El código del sitio.</li>
    </ul>
    <label class="a-check"><input type="checkbox" name="con_config"> Incluir <code>config.php</code> (tiene la contraseña de MySQL)</label>
    <button class="a-btn a-btn-primary" type="submit"<?= $zipOk ? '' : ' disabled' ?>>Descargar respaldo completo</button>
    <?php if (!$zipOk): ?><p class="a-error">Este hosting no tiene la extensión ZIP de PHP. Usa la descarga de la base de datos.</p><?php endif; ?>
  </form>

  <form class="panel a-backup-card" method="post">
    <?= csrf_field() ?><input type="hidden" name="tipo" value="base">
    <span class="a-backup-icon" aria-hidden="true"><?= icon('escudo') ?></span>
    <h2>Solo base de datos</h2>
    <p>Un archivo <code>.sql</code> liviano con productos, pedidos, cotizaciones y ajustes. Es lo que más cambia día a día.</p>
    <p class="hint">Para restaurarlo: en el hosting, abre <b>phpMyAdmin</b>, elige la base y usa <b>Importar</b> con este archivo.</p>
    <button class="a-btn" type="submit">Descargar base de datos (.sql)</button>
  </form>
</div>
<?php admin_footer(); ?>
