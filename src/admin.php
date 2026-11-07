<?php

declare(strict_types=1);

// Panel de administración: sesión, seguridad y plantilla común.
define('BASE', '../');
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/catalog.php';

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
  <link rel="stylesheet" href="../assets/admin.css?v=3">
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
