<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $used = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $used->execute([$id]);
        if ((int) $used->fetchColumn() > 0) {
            flash('No se puede eliminar: la categoría tiene productos. Muévelos u ocúltala.', 'error');
        } else {
            $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            flash('Categoría eliminada.');
        }
        redirect('categorias.php');
    }
    $name = trim((string) ($_POST['name'] ?? ''));
    if ($name === '') {
        flash('Escribe el nombre de la categoría.', 'error');
        redirect('categorias.php');
    }
    $slug = slugify(trim((string) ($_POST['slug'] ?? '')) ?: $name);
    $image = trim((string) ($_POST['image'] ?? ''));
    try {
        $image = save_upload('image_file') ?? $image;
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        redirect('categorias.php');
    }
    $values = [$slug, $name, trim((string) ($_POST['description'] ?? '')), $image, isset($_POST['active']) ? 1 : 0, (int) ($_POST['sort_order'] ?? 0)];
    try {
        if ($id > 0) {
            $values[] = $id;
            $pdo->prepare('UPDATE categories SET slug=?, name=?, description=?, image=?, active=?, sort_order=? WHERE id=?')->execute($values);
        } else {
            $pdo->prepare('INSERT INTO categories (slug, name, description, image, active, sort_order) VALUES (?,?,?,?,?,?)')->execute($values);
        }
        flash('Categoría guardada.');
    } catch (PDOException $exception) {
        flash('Ya existe una categoría con esa dirección (slug).', 'error');
    }
    redirect('categorias.php');
}

$rows = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS total FROM categories c ORDER BY c.sort_order, c.name')->fetchAll();
$rows[] = ['id' => 0, 'slug' => '', 'name' => '', 'description' => '', 'image' => '', 'active' => 1, 'sort_order' => count($rows), 'total' => 0];

admin_header('Categorías', 'categorias');
?>
<div class="a-head"><h1>Categorías</h1></div>
<div class="a-cats">
  <?php foreach ($rows as $row): $new = (int) $row['id'] === 0; ?>
    <form class="panel a-cat" method="post" enctype="multipart/form-data">
      <h2><?= $new ? 'Nueva categoría' : e($row['name']) ?><?php if (!$new): ?> <small><?= (int) $row['total'] ?> productos</small><?php endif; ?></h2>
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
      <div class="a-grid-2">
        <label>Nombre <input name="name" value="<?= e($row['name']) ?>" <?= $new ? '' : 'required' ?>></label>
        <label>Dirección (slug) <input name="slug" value="<?= e($row['slug']) ?>" placeholder="se genera del nombre"></label>
      </div>
      <label>Descripción <input name="description" maxlength="255" value="<?= e($row['description']) ?>"></label>
      <div class="a-grid-2">
        <label>Imagen <input name="image" value="<?= e($row['image']) ?>" placeholder="https://... o uploads/..."></label>
        <label>o subir <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp"></label>
      </div>
      <div class="a-row">
        <label class="a-check"><input type="checkbox" name="active" <?= (int) $row['active'] === 1 ? 'checked' : '' ?>> Visible</label>
        <label class="a-inline">Orden <input name="sort_order" type="number" value="<?= (int) $row['sort_order'] ?>"></label>
        <button class="a-btn a-btn-primary" type="submit"><?= $new ? 'Crear' : 'Guardar' ?></button>
      </div>
    </form>
    <?php if (!$new): ?>
      <form method="post" class="a-cat-del" data-confirm="¿Eliminar la categoría «<?= e($row['name']) ?>»?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="action" value="delete"><button class="a-link-danger" type="submit">Eliminar «<?= e($row['name']) ?>»</button></form>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
<?php admin_footer(); ?>
