<?php

declare(strict_types=1);

const ROOT = __DIR__ . '/..';

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
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    } catch (PDOException $exception) {
        error_log('RedTech DB: ' . $exception->getMessage());
        $pdo = null;
    }
    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Datos editables del sitio. Si MySQL no responde, usa los valores iniciales. */
function site_data(): array
{
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
        return $defaults;
    }
    return $data;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Imagen adaptable. Las de Unsplash se piden al tamaño justo; una ruta local se usa tal cual. */
function picture(array $image, string $sizes, int $width, int $height, bool $eager = false, string $class = ''): string
{
    $attributes = 'alt="' . e($image['alt']) . '" width="' . $width . '" height="' . $height . '" sizes="' . e($sizes) . '"'
        . ($class !== '' ? ' class="' . e($class) . '"' : '')
        . ($eager ? ' fetchpriority="high"' : ' loading="lazy"') . ' decoding="async"';

    if (!str_starts_with($image['url'], 'https://images.unsplash.com/')) {
        return '<img src="' . e($image['url']) . '" ' . $attributes . '>';
    }

    $variant = static function (int $w) use ($image, $width, $height): string {
        return $image['url'] . '?auto=format&fit=crop&q=70&w=' . $w . '&h=' . (int) round($w * $height / $width);
    };
    $srcset = implode(', ', array_map(static fn (int $w): string => $variant($w) . ' ' . $w . 'w', [480, 800, 1200, 1800]));
    return '<img src="' . e($variant(800)) . '" srcset="' . e($srcset) . '" ' . $attributes . '>';
}

function icon(string $name): string
{
    $paths = [
        'cableado' => '<rect x="6" y="6" width="36" height="36" rx="3"/><path d="M14 34V21h5v-6h10v6h5v13z"/><path d="M20 34v-6M24 34v-6M28 34v-6"/>',
        'camaras' => '<path d="M5 13h28l4 12H9z"/><path d="M37 16l7-3v10l-7-2"/><circle cx="16" cy="19" r="2.5"/><path d="M19 25v8H7v9"/>',
        'servidores' => '<rect x="7" y="6" width="34" height="10" rx="2"/><rect x="7" y="19" width="34" height="10" rx="2"/><rect x="7" y="32" width="34" height="10" rx="2"/><path d="M13 11h4M13 24h4M13 37h4M33 11h2M33 24h2M33 37h2"/>',
        'routers' => '<rect x="5" y="27" width="38" height="13" rx="2"/><path d="M13 27V11M35 27V11"/><path d="M11 34h3M18 34h3M25 34h3"/><path d="M18 14a8 8 0 0 1 12 0M21 18a4 4 0 0 1 6 0"/>',
        'tienda' => '<path d="M9 16h30l3 26H6z"/><path d="M17 21v-8a7 7 0 0 1 14 0v8"/>',
        'flecha' => '<path d="M6 24h34M28 11l13 13-13 13"/>',
        'check' => '<path d="M10 25l9 9 19-20"/>',
        'chat' => '<path d="M8 40l3-9a17 17 0 1 1 7 6z"/>',
    ];
    return '<svg class="icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
}
