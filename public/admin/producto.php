<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order, name')->fetchAll();
$errors = [];

$product = ['id' => 0, 'category_id' => $categories[0]['id'] ?? 0, 'slug' => '', 'name' => '', 'brand' => '', 'summary' => '', 'description' => '', 'specs' => '', 'price' => null, 'stock' => 'disponible', 'image' => '', 'gallery' => '', 'featured' => 0, 'active' => 1, 'sort_order' => 0];
if ($id > 0) {
    $statement = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $statement->execute([$id]);
    $found = $statement->fetch();
    if (!$found) {
        flash('Ese producto no existe.', 'error');
        redirect('productos.php');
    }
    $product = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $input = [
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'name' => trim((string) ($_POST['name'] ?? '')),
        'slug' => trim((string) ($_POST['slug'] ?? '')),
        'brand' => trim((string) ($_POST['brand'] ?? '')),
        'summary' => trim((string) ($_POST['summary'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'specs' => trim((string) ($_POST['specs'] ?? '')),
        'price' => trim(str_replace(',', '.', (string) ($_POST['price'] ?? ''))),
        'stock' => (string) ($_POST['stock'] ?? 'disponible'),
        'image' => trim((string) ($_POST['image'] ?? '')),
        'gallery' => trim((string) ($_POST['gallery'] ?? '')),
        'featured' => isset($_POST['featured']) ? 1 : 0,
        'active' => isset($_POST['active']) ? 1 : 0,
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];
    $input['slug'] = slugify($input['slug'] !== '' ? $input['slug'] : $input['name']);

    if ($input['name'] === '' || mb_strlen($input['name']) > 160) {
        $errors['name'] = 'Escribe el nombre del producto (máximo 160 caracteres).';
    }
    if (!in_array($input['category_id'], array_map('intval', array_column($categories, 'id')), true)) {
        $errors['category_id'] = 'Elige una categoría.';
    }
    if ($input['price'] !== '' && (!is_numeric($input['price']) || (float) $input['price'] < 0)) {
        $errors['price'] = 'Escribe el precio con números, por ejemplo 145.50. Déjalo vacío para "precio a consultar".';
    }
    if (!in_array($input['stock'], ['disponible', 'bajo_pedido', 'agotado'], true)) {
        $input['stock'] = 'disponible';
    }
    $exists = $pdo->prepare('SELECT id FROM products WHERE slug = ? AND id <> ?');
    $exists->execute([$input['slug'], $id]);
    if ($exists->fetch()) {
        $input['slug'] .= '-' . bin2hex(random_bytes(2));
    }

    try {
        if (($uploaded = save_upload('image_file')) !== null) {
            $input['image'] = $uploaded;
        }
        $extra = $_FILES['gallery_files'] ?? null;
        if ($extra && is_array($extra['name'])) {
            foreach (array_keys($extra['name']) as $k) {
                $_FILES['__g'] = ['name' => $extra['name'][$k], 'type' => $extra['type'][$k], 'tmp_name' => $extra['tmp_name'][$k], 'error' => $extra['error'][$k], 'size' => $extra['size'][$k]];
                if (($path = save_upload('__g')) !== null) {
                    $input['gallery'] = trim($input['gallery'] . "\n" . $path);
                }
            }
        }
    } catch (RuntimeException $exception) {
        $errors['image'] = $exception->getMessage();
    }

    if ($errors === []) {
        $values = [$input['category_id'], $input['slug'], $input['name'], $input['brand'], $input['summary'], $input['description'], $input['specs'],
            $input['price'] === '' ? null : round((float) $input['price'], 2), $input['stock'], $input['image'], $input['gallery'], $input['featured'], $input['active'], $input['sort_order']];
        if ($id > 0) {
            $values[] = $id;
            $pdo->prepare('UPDATE products SET category_id=?, slug=?, name=?, brand=?, summary=?, description=?, specs=?, price=?, stock=?, image=?, gallery=?, featured=?, active=?, sort_order=? WHERE id=?')->execute($values);
        } else {
            $pdo->prepare('INSERT INTO products (category_id, slug, name, brand, summary, description, specs, price, stock, image, gallery, featured, active, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute($values);
            $id = (int) $pdo->lastInsertId();
        }
        flash('Producto guardado.');
        redirect('producto.php?id=' . $id);
    }
    $product = array_merge($product, $input);
}

$err = static fn (string $k): string => isset($errors[$k]) ? '<p class="a-error">' . e($errors[$k]) . '</p>' : '';
admin_header($id > 0 ? 'Editar producto' : 'Nuevo producto', 'productos');
?>
<div class="a-head">
  <h1><?= $id > 0 ? 'Editar producto' : 'Nuevo producto' ?></h1>
  <div class="a-row">
    <?php if ($id > 0 && (int) $product['active'] === 1): ?><a class="a-btn" href="../producto.php?p=<?= e($product['slug']) ?>" target="_blank" rel="noopener">Ver en la tienda</a><?php endif; ?>
    <a class="a-btn" href="productos.php">Volver</a>
  </div>
</div>
<?php if ($errors): ?><p class="a-flash a-flash-error" role="alert">Revisa los campos marcados.</p><?php endif; ?>
<form class="a-form" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $id ?>">
  <div class="a-form-main">
    <section class="panel">
      <h2>Información</h2>
      <label>Nombre <input name="name" required maxlength="160" value="<?= e($product['name']) ?>"></label><?= $err('name') ?>
      <div class="a-grid-2">
        <label>Categoría
          <select name="category_id"><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"<?= (int) $product['category_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
        </label>
        <label>Marca <input name="brand" maxlength="80" value="<?= e($product['brand']) ?>"></label>
      </div>
      <?= $err('category_id') ?>
      <label>Resumen corto <input name="summary" maxlength="255" value="<?= e($product['summary']) ?>"><small>Aparece en la tarjeta del producto.</small></label>
      <label>Descripción <textarea name="description" rows="5"><?= e($product['description']) ?></textarea></label>
      <label>Especificaciones <textarea name="specs" rows="6" placeholder="Categoría: 6&#10;Longitud: 305 m"><?= e($product['specs']) ?></textarea><small>Una por línea, con el formato <code>Nombre: valor</code>.</small></label>
    </section>
    <section class="panel">
      <h2>Imágenes</h2>
      <div class="a-image">
        <img id="prev" src="<?= e(image_src($product['image'], 320, 256)) ?>" alt="" width="160" height="128">
        <div>
          <label>Subir foto principal <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="#prev"><small>JPG, PNG o WEBP, máximo 4 MB. Mejor si es cuadrada o 5:4.</small></label>
          <label>o dirección de la imagen <input name="image" value="<?= e($product['image']) ?>" placeholder="https://... o uploads/..."></label>
        </div>
      </div>
      <?= $err('image') ?>
      <label>Más fotos (galería) <input type="file" name="gallery_files[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"></label>
      <label>Galería actual <textarea name="gallery" rows="3" placeholder="Una dirección por línea"><?= e(is_array($product['gallery']) ? implode("\n", $product['gallery']) : (string) $product['gallery']) ?></textarea><small>Borra una línea para quitar esa foto.</small></label>
    </section>
  </div>
  <aside class="a-form-side">
    <section class="panel">
      <h2>Venta</h2>
      <label>Precio (USD) <input name="price" inputmode="decimal" value="<?= e($product['price'] === null ? '' : (string) $product['price']) ?>" placeholder="Vacío = a consultar"></label><?= $err('price') ?>
      <label>Disponibilidad
        <select name="stock"><?php foreach (['disponible', 'bajo_pedido', 'agotado'] as $s): ?><option value="<?= $s ?>"<?= $product['stock'] === $s ? ' selected' : '' ?>><?= e(stock_label($s)) ?></option><?php endforeach; ?></select>
      </label>
      <label class="a-check"><input type="checkbox" name="featured" <?= (int) $product['featured'] === 1 ? 'checked' : '' ?>> Destacado en el inicio</label>
      <label class="a-check"><input type="checkbox" name="active" <?= (int) $product['active'] === 1 ? 'checked' : '' ?>> Visible en la tienda</label>
    </section>
    <section class="panel">
      <h2>Avanzado</h2>
      <label>Dirección (slug) <input name="slug" value="<?= e($product['slug']) ?>" placeholder="se genera del nombre"></label>
      <label>Orden <input name="sort_order" type="number" value="<?= (int) $product['sort_order'] ?>"><small>Los números menores aparecen primero.</small></label>
    </section>
    <button class="a-btn a-btn-primary a-btn-block" type="submit">Guardar producto</button>
  </aside>
</form>
<?php admin_footer(); ?>
