<?php

declare(strict_types=1);

// Panel de administración: sesión, seguridad y plantilla común.
define('BASE', '../');
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/catalog.php';
require_once __DIR__ . '/install.php';

start_session();
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

function admin_user(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): PDO
{
    $pdo = db();
    if ($pdo === null) {
        admin_header('Base de datos');
        echo '<div class="panel"><h1>Falta la base de datos</h1><p>Configura MySQL en <code>config/config.php</code> y ejecuta la instalación. Revisa el README.</p></div>';
        admin_footer();
        exit;
    }
    if (admin_user() === null) {
        header('Location: index.php');
        exit;
    }
    // La sesión del panel caduca tras 2 horas sin actividad.
    if (time() - ($_SESSION['admin_seen'] ?? 0) > 7200) {
        unset($_SESSION['admin']);
        header('Location: index.php?caducada=1');
        exit;
    }
    $_SESSION['admin_seen'] = time();
    // Pone al día la base del hosting la primera vez que se entra con una versión nueva.
    if (($_SESSION['schema'] ?? 0) < SCHEMA_VERSION) {
        try {
            migrate_schema($pdo);
            $_SESSION['schema'] = SCHEMA_VERSION;
        } catch (PDOException $exception) {
            error_log('RedTech migración: ' . $exception->getMessage());
        }
    }
    return $pdo;
}

/** Verifica el token CSRF de un POST; si falla, corta la petición. */
function check_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(400);
        exit('Solicitud no válida. Vuelve atrás y recarga la página.');
    }
}

function flash(?string $message = null, string $type = 'ok'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $to): never
{
    header('Location: ' . $to, true, 303);
    exit;
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = strtr($text, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim((string) $text, '-') ?: 'item-' . bin2hex(random_bytes(3));
}

/**
 * Guarda una imagen subida en public/uploads y devuelve su ruta relativa (uploads/...),
 * o null si no se subió ninguna. Lanza RuntimeException si el archivo no es válido.
 */
function save_upload(string $field): ?string
{
    $file = $_FILES[$field] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo subir la imagen. Prueba con un archivo más liviano.');
    }
    if ($file['size'] > 4 * 1024 * 1024) {
        throw new RuntimeException('La imagen supera los 4 MB.');
    }
    $info = @getimagesize($file['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!$info || !isset($types[$info[2]])) {
        throw new RuntimeException('Sube una imagen JPG, PNG, WEBP o GIF.');
    }
    $dir = PUBLIC_DIR . '/uploads/' . date('Ym');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('No se pudo crear la carpeta de imágenes.');
    }
    $name = bin2hex(random_bytes(8)) . '.' . $types[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('No se pudo guardar la imagen.');
    }
    return 'uploads/' . date('Ym') . '/' . $name;
}

/**
 * Guarda una factura (PDF, JPG, PNG o WEBP) fuera de la carpeta pública, en storage/facturas.
 * Devuelve su ruta relativa a la raíz del proyecto, o null si no se subió nada.
 */
function save_invoice(string $field): ?string
{
    $file = $_FILES[$field] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('No se pudo subir la factura. Debe pesar menos de 8 MB.');
    }
    $head = (string) file_get_contents($file['tmp_name'], false, null, 0, 5);
    $info = @getimagesize($file['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if ($head === '%PDF-') {
        $ext = 'pdf';
    } elseif ($info && isset($types[$info[2]])) {
        $ext = $types[$info[2]];
    } else {
        throw new RuntimeException('La factura debe ser un PDF o una imagen JPG, PNG o WEBP.');
    }
    $relative = 'storage/facturas/' . date('Ym');
    $dir = ROOT . '/' . $relative;
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('No se pudo crear la carpeta de facturas.');
    }
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('No se pudo guardar la factura.');
    }
    return $relative . '/' . $name;
}

/**
 * Descuenta unidades vendidas del stock. Los productos sin control de stock (vacío) no cambian.
 * Si un producto llega a 0, queda como «Agotado». Devuelve true si el producto lleva control de stock.
 */
function discount_stock(PDO $pdo, int $productId, int $qty): bool
{
    $statement = $pdo->prepare('UPDATE products SET stock_qty = GREATEST(stock_qty - ?, 0) WHERE id = ? AND stock_qty IS NOT NULL');
    $statement->execute([max(0, $qty), $productId]);
    if ($statement->rowCount() === 0) {
        return false;
    }
    $pdo->prepare("UPDATE products SET stock = 'agotado' WHERE id = ? AND stock_qty = 0")->execute([$productId]);
    return true;
}

/** Estados de un pedido: [nombre visible, clase de etiqueta]. */
function order_statuses(): array
{
    return [
        'nuevo' => ['Nuevo', 'tag-new'],
        'atendido' => ['Atendido', 'tag-bajo_pedido'],
        'vendido' => ['Venta realizada', 'tag-disponible'],
        'no_concretado' => ['No se concretó', 'tag-agotado'],
        'cancelado' => ['No se concretó', 'tag-agotado'],
    ];
}

function admin_header(string $title, string $active = ''): void
{
    $user = admin_user();
    $links = [
        'inicio' => ['index.php', 'Resumen', 'tienda'],
        'productos' => ['productos.php', 'Productos', 'carrito'],
        'categorias' => ['categorias.php', 'Categorías', 'cables'],
        'pedidos' => ['pedidos.php', 'Pedidos', 'whatsapp'],
        'solicitudes' => ['solicitudes.php', 'Cotizaciones', 'correo'],
        'ajustes' => ['ajustes.php', 'Datos del sitio', 'soporte'],
    ];
    ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?> | Panel RedTech</title>
  <link rel="icon" href="../assets/img/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="../assets/img/favicon-32.png" type="image/png" sizes="32x32">
  <link rel="apple-touch-icon" href="../assets/img/apple-touch-icon.png">
  <link rel="stylesheet" href="../assets/admin.css?v=5">
  <?= brand_style() ?>
</head>
<body class="<?= $user ? 'is-auth' : 'is-guest' ?>">
<?php if ($user): ?>
  <header class="a-top">
    <a class="a-logo" href="index.php"><?= site_logo()[1] ?> <small>Panel</small></a>
    <nav class="a-nav" aria-label="Panel">
      <?php foreach ($links as $key => [$href, $label, $ico]): ?>
        <a href="<?= $href ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>><?= icon($ico) ?><span><?= $label ?></span></a>
      <?php endforeach; ?>
    </nav>
    <div class="a-user">
      <a href="../" target="_blank" rel="noopener">Ver sitio</a>
      <form method="post" action="salir.php"><?= csrf_field() ?><button type="submit">Salir (<?= e($user['username']) ?>)</button></form>
    </div>
  </header>
<?php endif; ?>
<main class="a-main">
<?php if ($flash = flash()): ?>
  <p class="a-flash a-flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></p>
<?php endif;
}

function admin_footer(): void
{
    ?>
</main>
<script>
document.querySelectorAll('[data-confirm]').forEach(f => f.addEventListener('submit', e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); }));
document.querySelectorAll('input[type=file][data-preview]').forEach(i => i.addEventListener('change', () => {
  const img = document.querySelector(i.dataset.preview); if (img && i.files[0]) { img.src = URL.createObjectURL(i.files[0]); img.hidden = false; const empty = img.nextElementSibling; if (empty && empty.tagName === "SPAN") empty.remove(); }
}));
</script>
</body>
</html>
<?php
}
