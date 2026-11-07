<?php

declare(strict_types=1);

const ROOT = __DIR__ . '/..';

// Prefijo para enlaces y recursos: '' en las páginas públicas, '../' en /admin.
if (!defined('BASE')) {
    define('BASE', '');
}

function config(): array
{
    static $config = null;
    if ($config === null) {
        $path = ROOT . '/config/config.php';
        $config = is_file($path) ? require $path : [];
    }
    return $config;
}

function db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;
    if ($attempted) {
        return $pdo;
    }
    $attempted = true;

    $db = config()['db'] ?? null;
    if ($db === null) {
        return null;
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], $db['port'] ?? 3306, $db['name']),
            $db['user'],
            $db['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
    } catch (PDOException $exception) {
        error_log('RedTech DB: ' . $exception->getMessage());
        $pdo = null;
    }
    return $pdo;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']), 'path' => '/']);
    session_start();
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE . ltrim($path, '/');
}

/** Datos editables del sitio. Si MySQL no responde, usa los valores iniciales. */
function site_data(): array
{
    static $data = null;
    if ($data !== null) {
        return $data;
    }
    $defaults = require ROOT . '/config/defaults.php';
    $data = $defaults;
    $pdo = db();
    if ($pdo === null) {
        return $data;
    }

    try {
        $settings = [];
        foreach ($pdo->query('SELECT setting_key, setting_value, confirmed FROM settings') as $row) {
            $settings[$row['setting_key']] = ['value' => $row['setting_value'], 'confirmed' => (int) $row['confirmed']];
        }
        $data['settings'] = $settings + $defaults['settings'];
        $images = [];
        foreach ($pdo->query('SELECT slot, url, alt FROM images') as $row) {
            $images[$row['slot']] = ['url' => $row['url'], 'alt' => $row['alt']];
        }
        $data['images'] = $images + $defaults['images'];
        $data['contacts'] = $pdo->query('SELECT type, label, value, confirmed FROM contacts WHERE active = 1 ORDER BY sort_order, id')->fetchAll();
        $data['stats'] = $pdo->query('SELECT value, label, confirmed FROM stats WHERE active = 1 ORDER BY sort_order, id')->fetchAll();
    } catch (PDOException $exception) {
        error_log('RedTech DB: ' . $exception->getMessage());
        $data = $defaults;
    }
    return $data;
}

function setting(string $key): string
{
    return (string) (site_data()['settings'][$key]['value'] ?? '');
}

/** Número para wa.me: el ajuste "whatsapp" o, si falta, el primer teléfono confirmado. */
function whatsapp_number(): string
{
    $number = preg_replace('/\D/', '', setting('whatsapp'));
    if ($number !== '') {
        return $number;
    }
    foreach (site_data()['contacts'] as $contact) {
        if ($contact['type'] === 'phone' && (int) $contact['confirmed'] === 1) {
            return preg_replace('/\D/', '', $contact['value']);
        }
    }
    return '';
}

function whatsapp_link(string $message = ''): string
{
    $number = whatsapp_number();
    if ($number === '') {
        return '';
    }
    return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
}

/** Dirección del mapa insertado de Google Maps: un enlace "embed" o, si no, una búsqueda por dirección. */
function map_embed_src(): string
{
    $value = trim(setting('map_embed'));
    if (str_starts_with($value, 'https://www.google.com/maps/embed')) {
        return $value;
    }
    $query = $value !== '' ? $value : setting('location');
    return $query === '' ? '' : 'https://www.google.com/maps?q=' . rawurlencode($query) . '&z=15&output=embed';
}

function map_directions_url(): string
{
    if (setting('map_url') !== '') {
        return setting('map_url');
    }
    $value = trim(setting('map_embed'));
    $query = $value !== '' && !str_starts_with($value, 'http') ? $value : setting('location');
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    return hash_equals($_SESSION['csrf'] ?? '', (string) $token);
}

/** URL de una imagen: las de Unsplash se piden recortadas al tamaño; las locales se resuelven desde la raíz pública. */
function image_src(string $url, int $width, int $height): string
{
    if ($url === '') {
        return url('assets/img/placeholder.svg');
    }
    if (str_starts_with($url, 'https://images.unsplash.com/')) {
        return $url . '?auto=format&fit=crop&q=70&w=' . $width . '&h=' . $height;
    }
    if (preg_match('#^https?://#', $url)) {
        return $url;
    }
    return url($url);
}

/** Imagen adaptable con srcset para Unsplash; una ruta local se usa tal cual. */
function picture(array $image, string $sizes, int $width, int $height, bool $eager = false, string $class = '', ?float $parallax = null): string
{
    $attributes = 'alt="' . e($image['alt'] ?? '') . '" width="' . $width . '" height="' . $height . '" sizes="' . e($sizes) . '"'
        . ($class !== '' ? ' class="' . e($class) . '"' : '')
        . ($parallax !== null ? ' data-parallax="' . $parallax . '"' : '')
        . ($eager ? ' fetchpriority="high"' : ' loading="lazy"') . ' decoding="async"';
    $src = (string) ($image['url'] ?? '');

    if (!str_starts_with($src, 'https://images.unsplash.com/')) {
        return '<img src="' . e(image_src($src, $width, $height)) . '" ' . $attributes . '>';
    }
    $variant = static fn (int $w): string => image_src($src, $w, (int) round($w * $height / $width));
    $srcset = implode(', ', array_map(static fn (int $w): string => $variant($w) . ' ' . $w . 'w', [400, 700, 1000, 1400, 1900]));
    return '<img src="' . e($variant(min(1000, $width))) . '" srcset="' . e($srcset) . '" ' . $attributes . '>';
}

function icon(string $name, string $class = 'icon'): string
{
    $paths = [
        'cableado' => '<rect x="6" y="6" width="36" height="36" rx="4"/><path d="M14 34V21h5v-6h10v6h5v13z"/><path d="M20 34v-6M24 34v-6M28 34v-6"/>',
        'camaras' => '<path d="M5 13h28l4 12H9z"/><path d="M37 16l7-3v10l-7-2"/><circle cx="16" cy="19" r="2.5"/><path d="M19 25v8H7v9"/>',
        'servidores' => '<rect x="7" y="6" width="34" height="10" rx="2"/><rect x="7" y="19" width="34" height="10" rx="2"/><rect x="7" y="32" width="34" height="10" rx="2"/><path d="M13 11h4M13 24h4M13 37h4M33 11h2M33 24h2M33 37h2"/>',
        'routers' => '<rect x="5" y="27" width="38" height="13" rx="2"/><path d="M13 27V11M35 27V11"/><path d="M11 34h3M18 34h3M25 34h3"/><path d="M18 14a8 8 0 0 1 12 0M21 18a4 4 0 0 1 6 0"/>',
        'cables' => '<path d="M10 6v10a6 6 0 0 0 6 6h16a6 6 0 0 1 6 6v14"/><rect x="5" y="3" width="10" height="7" rx="1.5"/><rect x="33" y="38" width="10" height="7" rx="1.5"/>',
        'wifi' => '<path d="M6 19a26 26 0 0 1 36 0M12 26a17 17 0 0 1 24 0M18 33a8 8 0 0 1 12 0"/><circle cx="24" cy="39" r="1.5"/>',
        'soporte' => '<path d="M30 8a9 9 0 0 0-11 11L7 31a4 4 0 0 0 6 6l12-12a9 9 0 0 0 11-11l-6 6-5-1-1-5z"/>',
        'escudo' => '<path d="M24 4l16 6v12c0 10-7 18-16 22C15 40 8 32 8 22V10z"/><path d="M17 24l5 5 9-10"/>',
        'tienda' => '<path d="M9 16h30l3 26H6z"/><path d="M17 21v-8a7 7 0 0 1 14 0v8"/>',
        'carrito' => '<path d="M4 6h6l5 24h22l5-17H13"/><circle cx="18" cy="38" r="3"/><circle cx="34" cy="38" r="3"/>',
        'buscar' => '<circle cx="21" cy="21" r="13"/><path d="M31 31l11 11"/>',
        'flecha' => '<path d="M6 24h34M28 11l13 13-13 13"/>',
        'atras' => '<path d="M42 24H8M20 11L7 24l13 13"/>',
        'check' => '<path d="M10 25l9 9 19-20"/>',
        'mas' => '<path d="M24 10v28M10 24h28"/>',
        'menos' => '<path d="M10 24h28"/>',
        'cerrar' => '<path d="M12 12l24 24M36 12L12 36"/>',
        'menu' => '<path d="M7 13h34M7 24h34M7 35h34"/>',
        'basura' => '<path d="M8 13h32M19 13V7h10v6M12 13l2 28h20l2-28"/>',
        'telefono' => '<path d="M14 5l6 9-4 4a24 24 0 0 0 14 14l4-4 9 6-3 7C22 41 7 26 7 8z"/>',
        'correo' => '<rect x="5" y="10" width="38" height="28" rx="3"/><path d="M6 12l18 14 18-14"/>',
        'ubicacion' => '<path d="M24 44S9 29 9 19a15 15 0 0 1 30 0c0 10-15 25-15 25z"/><circle cx="24" cy="19" r="5"/>',
        'reloj' => '<circle cx="24" cy="24" r="18"/><path d="M24 13v12l8 5"/>',
        'camion' => '<path d="M3 12h26v20H3zM29 19h9l7 7v6H29z"/><circle cx="12" cy="36" r="4"/><circle cx="37" cy="36" r="4"/>',
        'chat' => '<path d="M8 40l3-9a17 17 0 1 1 7 6z"/>',
        'whatsapp' => '<path d="M7 41l2.6-9.4A17 17 0 1 1 16 38z"/><path d="M18 15c-1 0-2.5 1.2-2.5 3.5 0 4 5.5 11 12 12.5 2 .5 4-1 4.5-2.5l-4-2.2-2 2c-2.5-1-5.4-3.8-6.3-6.3l2-2L19.5 15z"/>',
        'facebook' => '<path d="M28 44V27h6l1-7h-7v-4c0-2 1-3 3-3h4V7a40 40 0 0 0-6-.5c-6 0-9 3.5-9 9.5v4h-6v7h6v17"/>',
        'instagram' => '<rect x="6" y="6" width="36" height="36" rx="10"/><circle cx="24" cy="24" r="8"/><circle cx="35" cy="13" r="1.5"/>',
        'tiktok' => '<path d="M28 6v24a7 7 0 1 1-7-7M28 6c1 5 5 9 11 9"/>',
        'estrella' => '<path d="M24 5l5.9 12 13.1 1.9-9.5 9.2 2.3 13L24 35l-11.8 6.1 2.3-13L5 18.9 18.1 17z"/>',
    ];
    return '<svg class="' . e($class) . '" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}
