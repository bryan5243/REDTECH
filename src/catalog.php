<?php

declare(strict_types=1);

/** Catálogo: lee MySQL y, si no hay conexión, usa el catálogo de ejemplo de config/catalog.php. */

function catalog_seed(): array
{
    static $seed = null;
    if ($seed !== null) {
        return $seed;
    }
    $raw = require ROOT . '/config/catalog.php';
    $categories = [];
    foreach ($raw['categories'] as $i => $category) {
        $categories[$category['slug']] = $category + ['id' => $i + 1, 'active' => 1, 'sort_order' => $i];
    }
    $products = [];
    foreach ($raw['products'] as $i => $product) {
        $category = $categories[$product['category']];
        $products[] = [
            'id' => $i + 1,
            'slug' => $product['slug'],
            'name' => $product['name'],
            'brand' => $product['brand'],
            'summary' => $product['summary'],
            'description' => $product['description'],
            'specs' => $product['specs'],
            'price' => $product['price'],
            'stock' => $product['stock'],
            'image' => $product['image'],
            'gallery' => $product['gallery'],
            'featured' => $product['featured'],
            'sort_order' => $i,
            'category_id' => $category['id'],
            'category_slug' => $category['slug'],
            'category_name' => $category['name'],
        ];
    }
    return $seed = ['categories' => $categories, 'products' => $products];
}

function normalize_product(array $row): array
{
    $row['price'] = $row['price'] === null || $row['price'] === '' ? null : (float) $row['price'];
    $row['featured'] = (int) $row['featured'];
    $row['stock_qty'] = isset($row['stock_qty']) && $row['stock_qty'] !== '' ? (int) $row['stock_qty'] : null;
    $row['show_stock'] = (int) ($row['show_stock'] ?? 0);
    if ($row['stock_qty'] === 0) {
        $row['stock'] = 'agotado';
    }
    $row['gallery'] = array_values(array_filter(array_map('trim', explode("\n", (string) ($row['gallery'] ?? '')))));
    return $row;
}

/** Categorías activas, con el número de productos activos de cada una. */
function categories(): array
{
    $pdo = db();
    if ($pdo !== null) {
        try {
            $rows = $pdo->query(
                'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.active = 1) AS product_count
                 FROM categories c WHERE c.active = 1 ORDER BY c.sort_order, c.name'
            )->fetchAll();
            $out = [];
            foreach ($rows as $row) {
                $out[$row['slug']] = $row;
            }
            return $out;
        } catch (PDOException $exception) {
            error_log('RedTech DB: ' . $exception->getMessage());
        }
    }
    $seed = catalog_seed();
    $out = [];
    foreach ($seed['categories'] as $slug => $category) {
        $category['product_count'] = count(array_filter($seed['products'], static fn ($p) => $p['category_slug'] === $slug));
        $out[$slug] = $category;
    }
    return $out;
}

/**
 * Productos activos filtrados.
 * $filters: category (slug), q (texto), featured (bool), sort (destacados|precio-asc|precio-desc|nombre), limit, exclude (id)
 */
function products(array $filters = []): array
{
    $sort = $filters['sort'] ?? 'destacados';
    $limit = isset($filters['limit']) ? max(1, (int) $filters['limit']) : null;
    $pdo = db();

    if ($pdo !== null) {
        try {
            $where = ['p.active = 1', 'c.active = 1'];
            $params = [];
            if (!empty($filters['category'])) {
                $where[] = 'c.slug = ?';
                $params[] = $filters['category'];
            }
            if (!empty($filters['q'])) {
                $where[] = '(p.name LIKE ? OR p.summary LIKE ? OR p.brand LIKE ?)';
                $like = '%' . $filters['q'] . '%';
                array_push($params, $like, $like, $like);
            }
            if (!empty($filters['featured'])) {
                $where[] = 'p.featured = 1';
            }
            if (!empty($filters['exclude'])) {
                $where[] = 'p.id <> ?';
                $params[] = (int) $filters['exclude'];
            }
            $order = match ($sort) {
                'precio-asc' => 'p.price IS NULL, p.price ASC',
                'precio-desc' => 'p.price IS NULL, p.price DESC',
                'nombre' => 'p.name ASC',
                default => 'p.featured DESC, p.sort_order, p.id DESC',
            };
            $sql = 'SELECT p.*, c.slug AS category_slug, c.name AS category_name
                    FROM products p JOIN categories c ON c.id = p.category_id
                    WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order . ($limit ? ' LIMIT ' . $limit : '');
            $statement = $pdo->prepare($sql);
            $statement->execute($params);
            return array_map('normalize_product', $statement->fetchAll());
        } catch (PDOException $exception) {
            error_log('RedTech DB: ' . $exception->getMessage());
        }
    }

    $list = catalog_seed()['products'];
    if (!empty($filters['category'])) {
        $list = array_filter($list, static fn ($p) => $p['category_slug'] === $filters['category']);
    }
    if (!empty($filters['q'])) {
        $needle = mb_strtolower($filters['q']);
        $list = array_filter($list, static fn ($p) => str_contains(mb_strtolower($p['name'] . ' ' . $p['summary'] . ' ' . $p['brand']), $needle));
    }
    if (!empty($filters['featured'])) {
        $list = array_filter($list, static fn ($p) => $p['featured'] === 1);
    }
    if (!empty($filters['exclude'])) {
        $list = array_filter($list, static fn ($p) => $p['id'] !== (int) $filters['exclude']);
    }
    $list = array_values($list);
    usort($list, static function (array $a, array $b) use ($sort): int {
        $byPrice = static function (?float $x, ?float $y, int $dir): int {
            if ($x === null || $y === null) {
                return ($x === null) <=> ($y === null);
            }
            return $dir * ($x <=> $y);
        };
        return match ($sort) {
            'precio-asc' => $byPrice($a['price'], $b['price'], 1),
            'precio-desc' => $byPrice($a['price'], $b['price'], -1),
            'nombre' => strcmp($a['name'], $b['name']),
            default => [$b['featured'], $a['sort_order']] <=> [$a['featured'], $b['sort_order']],
        };
    });
    return $limit ? array_slice($list, 0, $limit) : $list;
}

function product_by_slug(string $slug): ?array
{
    $pdo = db();
    if ($pdo !== null) {
        try {
            $statement = $pdo->prepare(
                'SELECT p.*, c.slug AS category_slug, c.name AS category_name
                 FROM products p JOIN categories c ON c.id = p.category_id
                 WHERE p.slug = ? AND p.active = 1 AND c.active = 1'
            );
            $statement->execute([$slug]);
            $row = $statement->fetch();
            return $row ? normalize_product($row) : null;
        } catch (PDOException $exception) {
            error_log('RedTech DB: ' . $exception->getMessage());
        }
    }
    foreach (catalog_seed()['products'] as $product) {
        if ($product['slug'] === $slug) {
            return $product;
        }
    }
    return null;
}

function money(?float $price): string
{
    return $price === null ? 'Precio a consultar' : '$' . number_format($price, 2, '.', ',');
}

function stock_label(string $stock): string
{
    return ['disponible' => 'Disponible', 'bajo_pedido' => 'Bajo pedido', 'agotado' => 'Agotado'][$stock] ?? $stock;
}

/** Especificaciones "Clave: valor" (una por línea) como pares. */
function parse_specs(?string $specs): array
{
    $out = [];
    foreach (preg_split('/\R/', (string) $specs) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        [$key, $value] = array_pad(array_map('trim', explode(':', $line, 2)), 2, '');
        $out[] = [$key, $value];
    }
    return $out;
}

/** Datos del producto que usa el carrito en el navegador. */
function cart_payload(array $product): string
{
    return json_encode([
        'id' => (int) $product['id'],
        'slug' => $product['slug'],
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => image_src($product['image'], 200, 200),
        'max' => $product['stock_qty'] ?? null,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
}

/** Texto «Quedan N unidades» si el producto muestra su stock en la web; vacío si no. */
function stock_note(array $product): string
{
    $qty = $product['stock_qty'] ?? null;
    if (empty($product['show_stock']) || $qty === null || $qty <= 0) {
        return '';
    }
    return $qty === 1 ? 'Queda 1 unidad' : ($qty <= 5 ? "Últimas $qty unidades" : "$qty unidades disponibles");
}
