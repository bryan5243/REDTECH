<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = db();
$ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
$error = null;

if ($pdo === null) {
    require_admin(); // muestra el aviso de base de datos
}

$hasAdmins = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_user() === null) {
    check_post();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $recent = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > (NOW() - INTERVAL 15 MINUTE)');
    $recent->execute([$ip]);

    if ((int) $recent->fetchColumn() >= 5) {
        $error = 'Demasiados intentos. Espera 15 minutos e inténtalo de nuevo.';
    } elseif (!$hasAdmins) {
        // Primer administrador: requiere la clave de instalación de config/config.php.
        $setupKey = (string) (config()['admin_setup_key'] ?? '');
        if ($setupKey === '' || !hash_equals($setupKey, (string) ($_POST['setup_key'] ?? ''))) {
            $pdo->prepare('INSERT INTO login_attempts (ip) VALUES (?)')->execute([$ip]);
            $error = 'La clave de instalación no es correcta.';
        } elseif (!preg_match('/^[a-zA-Z0-9._-]{3,60}$/', $username)) {
            $error = 'El usuario debe tener entre 3 y 60 letras, números, puntos o guiones.';
        } elseif (mb_strlen($password) < 10) {
            $error = 'La contraseña debe tener al menos 10 caracteres.';
        } else {
            $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)')->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['admin'] = ['id' => (int) $pdo->lastInsertId(), 'username' => $username];
            $_SESSION['admin_seen'] = time();
            flash('Administrador creado. Ya puedes quitar admin_setup_key de config/config.php.');
            redirect('index.php');
        }
    } else {
        $statement = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ?');
        $statement->execute([$username]);
        $row = $statement->fetch();
        if ($row && password_verify($password, $row['password_hash'])) {
            if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
                $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $row['id']]);
            }
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([$ip]);
            session_regenerate_id(true);
            $_SESSION['admin'] = ['id' => (int) $row['id'], 'username' => $row['username']];
            $_SESSION['admin_seen'] = time();
            redirect('index.php');
        }
        $pdo->prepare('INSERT INTO login_attempts (ip) VALUES (?)')->execute([$ip]);
        $error = 'Usuario o contraseña incorrectos.';
    }
}

if (admin_user() === null) {
    admin_header($hasAdmins ? 'Ingresar' : 'Crear administrador');
    ?>
    <div class="a-login">
      <a class="a-logo" href="../"><span class="logo-mark" aria-hidden="true"><i></i><i></i><i></i></span><span>Red<b>Tech</b></span></a>
      <form class="panel" method="post">
        <h1><?= $hasAdmins ? 'Ingresar al panel' : 'Crear el primer administrador' ?></h1>
        <?php if (isset($_GET['caducada'])): ?><p class="a-flash a-flash-warn">Tu sesión caducó. Vuelve a ingresar.</p><?php endif; ?>
        <?php if ($error): ?><p class="a-flash a-flash-error" role="alert"><?= e($error) ?></p><?php endif; ?>
        <?php if (!$hasAdmins): ?>
          <p class="hint">Escribe la clave <code>admin_setup_key</code> que pusiste en <code>config/config.php</code>.</p>
          <label>Clave de instalación <input name="setup_key" type="password" required autocomplete="off"></label>
        <?php endif; ?>
        <?= csrf_field() ?>
        <label>Usuario <input name="username" required autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>"></label>
        <label>Contraseña <input name="password" type="password" required autocomplete="<?= $hasAdmins ? 'current-password' : 'new-password' ?>" <?= $hasAdmins ? '' : 'minlength="10"' ?>></label>
        <button class="a-btn a-btn-primary" type="submit"><?= $hasAdmins ? 'Ingresar' : 'Crear y entrar' ?></button>
      </form>
    </div>
    <?php
    admin_footer();
    exit;
}

$pdo = require_admin();
$counts = [
    'productos' => (int) $pdo->query('SELECT COUNT(*) FROM products WHERE active = 1')->fetchColumn(),
    'pedidos' => (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'nuevo'")->fetchColumn(),
    'solicitudes' => (int) $pdo->query('SELECT COUNT(*) FROM quote_requests WHERE created_at > (NOW() - INTERVAL 30 DAY)')->fetchColumn(),
    'categorias' => (int) $pdo->query('SELECT COUNT(*) FROM categories WHERE active = 1')->fetchColumn(),
];
$orders = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 5')->fetchAll();
$quotes = $pdo->query('SELECT * FROM quote_requests ORDER BY id DESC LIMIT 5')->fetchAll();
$checks = [];
if (whatsapp_number() === '') {
    $checks[] = ['Agrega el número de WhatsApp para recibir pedidos.', 'ajustes.php'];
}
foreach (site_data()['contacts'] as $contact) {
    if ((int) $contact['confirmed'] !== 1) {
        $checks[] = ['Confirma tu ' . mb_strtolower($contact['label']) . ' (' . $contact['value'] . ').', 'ajustes.php'];
    }
}
if (setting('catalog_sample') === '1') {
    $checks[] = ['El catálogo es de ejemplo: carga tus productos y desactiva el aviso.', 'productos.php'];
}

admin_header('Resumen', 'inicio');
?>
<div class="a-head"><h1>Hola, <?= e(admin_user()['username']) ?></h1><a class="a-btn a-btn-primary" href="producto.php"><?= icon('mas') ?> Nuevo producto</a></div>
<div class="a-cards">
  <a class="a-card" href="productos.php"><span><?= icon('carrito') ?></span><b><?= $counts['productos'] ?></b>Productos activos</a>
  <a class="a-card" href="pedidos.php"><span><?= icon('whatsapp') ?></span><b><?= $counts['pedidos'] ?></b>Pedidos nuevos</a>
  <a class="a-card" href="solicitudes.php"><span><?= icon('correo') ?></span><b><?= $counts['solicitudes'] ?></b>Cotizaciones (30 días)</a>
  <a class="a-card" href="categorias.php"><span><?= icon('cables') ?></span><b><?= $counts['categorias'] ?></b>Categorías</a>
</div>

<?php if ($checks): ?>
  <section class="panel">
    <h2>Pendientes para publicar</h2>
    <ul class="a-checks"><?php foreach ($checks as [$text, $href]): ?><li><a href="<?= $href ?>"><?= e($text) ?></a></li><?php endforeach; ?></ul>
  </section>
<?php endif; ?>

<div class="a-two">
  <section class="panel">
    <div class="a-head"><h2>Últimos pedidos</h2><a href="pedidos.php">Ver todos</a></div>
    <?php if (!$orders): ?><p class="hint">Aún no hay pedidos.</p><?php else: ?>
      <table class="a-table"><tbody>
        <?php foreach ($orders as $order): ?>
          <tr><td><b><?= e($order['customer_name']) ?></b><br><small><?= e($order['created_at']) ?></small></td><td><?= e(money((float) $order['total'])) ?></td><td><span class="tag tag-<?= e($order['status']) ?>"><?= e($order['status']) ?></span></td></tr>
        <?php endforeach; ?>
      </tbody></table>
    <?php endif; ?>
  </section>
  <section class="panel">
    <div class="a-head"><h2>Últimas cotizaciones</h2><a href="solicitudes.php">Ver todas</a></div>
    <?php if (!$quotes): ?><p class="hint">Aún no hay solicitudes.</p><?php else: ?>
      <table class="a-table"><tbody>
        <?php foreach ($quotes as $quote): ?>
          <tr><td><b><?= e($quote['name']) ?></b><br><small><?= e($quote['created_at']) ?></small></td><td><?= e($quote['interest']) ?></td><td><?= e($quote['phone'] ?: $quote['email']) ?></td></tr>
        <?php endforeach; ?>
      </tbody></table>
    <?php endif; ?>
  </section>
</div>
<?php admin_footer(); ?>
