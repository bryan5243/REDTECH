<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'toggle') {
        $pdo->prepare('UPDATE products SET active = 1 - active WHERE id = ?')->execute([$id]);
        flash('Producto actualizado.');
    } elseif ($action === 'featured') {
        $pdo->prepare('UPDATE products SET featured = 1 - featured WHERE id = ?')->execute([$id]);
        flash('Producto actualizado.');
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        flash('Producto eliminado.');
    }
    redirect('productos.php?' . http_build_query(array_filter(['q' => $_POST['q'] ?? '', 'categoria' => $_POST['categoria'] ?? ''])));
}

$q = trim((string) ($_GET['q'] ?? ''));
$cat = (int) ($_GET['categoria'] ?? 0);
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(p.name LIKE ? OR p.brand LIKE ?)';
    array_push($params, "%$q%", "%$q%");
}
if ($cat > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $cat;
}
$statement = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id'
    . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY p.sort_order, p.id DESC');
$statement->execute($params);
$rows = $statement->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order, name')->fetchAll();

admin_header('Productos', 'productos');
?>
<div class="a-head"><h1>Productos</h1><a class="a-btn a-btn-primary" href="producto.php"><?= icon('mas') ?> Nuevo producto</a></div>
<form class="a-filter" method="get">
  <input name="q" type="search" value="<?= e($q) ?>" placeholder="Buscar por nombre o marca" aria-label="Buscar">
  <select name="categoria" aria-label="Categoría">
    <option value="">Todas las categorías</option>
    <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"<?= $cat === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select>
  <button class="a-btn" type="submit">Filtrar</button>
</form>
<div class="panel panel-flush">
  <?php if (!$rows): ?><p class="hint pad">No hay productos con ese filtro.</p><?php else: ?>
  <table class="a-table a-table-products">
    <thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th><span class="sr-only">Acciones</span></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $row): $row = normalize_product($row); ?>
      <tr class="<?= (int) $row['active'] === 1 ? '' : 'is-off' ?>">
        <td><div class="a-prod"><img src="<?= e(image_src($row['image'], 96, 96)) ?>" alt="" width="48" height="48" loading="lazy"><div><a href="producto.php?id=<?= (int) $row['id'] ?>"><b><?= e($row['name']) ?></b></a><?php if ($row['featured']): ?> <span class="tag tag-star">Destacado</span><?php endif; ?><br><small><?= e($row['brand']) ?></small></div></div></td>
        <td data-label="Categoría"><?= e($row['category_name']) ?></td>
        <td data-label="Precio"><?= e(money($row['price'])) ?></td>
        <td data-label="Stock"><span class="tag tag-<?= e($row['stock']) ?>"><?= e(stock_label($row['stock'])) ?></span></td>
        <td data-label="Estado"><?= (int) $row['active'] === 1 ? 'Visible' : 'Oculto' ?></td>
        <td class="a-actions">
          <a class="a-btn a-btn-sm" href="producto.php?id=<?= (int) $row['id'] ?>">Editar</a>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="action" value="featured"><button class="a-btn a-btn-sm" type="submit"><?= $row['featured'] ? 'Quitar destacado' : 'Destacar' ?></button></form>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="action" value="toggle"><button class="a-btn a-btn-sm" type="submit"><?= (int) $row['active'] === 1 ? 'Ocultar' : 'Mostrar' ?></button></form>
          <form method="post" data-confirm="¿Eliminar «<?= e($row['name']) ?>»? No se puede deshacer."><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="action" value="delete"><button class="a-btn a-btn-sm a-btn-danger" type="submit">Eliminar</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
