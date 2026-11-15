<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();
$defaults = require ROOT . '/config/defaults.php';

$settingFields = [
    'whatsapp' => ['WhatsApp para pedidos', 'Solo números con código de país. Ej.: 593991234567', false],
    'location' => ['Ubicación', 'Ciudad, provincia y país', true],
    'schedule' => ['Horario de atención', 'Ej.: Lunes a viernes, 08:00 a 18:00', true],
    'map_embed' => ['Mapa en Contacto: dirección o enlace «Insertar un mapa»', 'Av. 25 de Junio y Guayas, Machala  o  https://www.google.com/maps/embed?...', false],
    'map_url' => ['Enlace «Cómo llegar» (opcional)', 'https://maps.app.goo.gl/...', false],
    'facebook' => ['Facebook', 'https://facebook.com/...', false],
    'instagram' => ['Instagram', 'https://instagram.com/...', false],
    'tiktok' => ['TikTok', 'https://tiktok.com/@...', false],
];
$imageLabels = ['hero' => 'Portada (fondo)', 'servidores' => 'Servicio: servidores y portada', 'cableado' => 'Servicio: cableado', 'camaras' => 'Servicio: cámaras', 'nosotros' => 'Sección "Por qué RedTech"', 'empresas' => 'Sector: oficinas', 'hogares' => 'Sector: hogares'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_post();
    $section = (string) ($_POST['section'] ?? '');

    if ($section === 'settings') {
        $save = $pdo->prepare('INSERT INTO settings (setting_key, setting_value, confirmed) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), confirmed = VALUES(confirmed)');
        foreach ($settingFields as $key => [, , $confirmable]) {
            $value = trim(mb_substr((string) ($_POST['s'][$key] ?? ''), 0, 500));
            if ($key === 'whatsapp') {
                $value = preg_replace('/\D/', '', $value);
            } elseif ($key === 'map_embed' && preg_match('#src="(https://www\.google\.com/maps/embed[^"]+)"#', $value, $m)) {
                // Si pegan el código <iframe> completo, guardamos solo la dirección.
                $value = html_entity_decode($m[1]);
            } elseif ($value !== '' && in_array($key, ['map_url', 'facebook', 'instagram', 'tiktok'], true) && !preg_match('#^https?://#i', $value)) {
                $value = 'https://' . $value;
            }
            $save->execute([$key, $value, $confirmable ? (isset($_POST['c'][$key]) ? 1 : 0) : 1]);
        }
        $save->execute(['catalog_sample', isset($_POST['catalog_sample']) ? '1' : '0', 1]);
        flash('Datos guardados.');
    } elseif ($section === 'contacts') {
        foreach ((array) ($_POST['contact'] ?? []) as $cid => $row) {
            $value = trim((string) ($row['value'] ?? ''));
            if (isset($row['delete']) || $value === '') {
                $pdo->prepare('DELETE FROM contacts WHERE id = ?')->execute([(int) $cid]);
                continue;
            }
            $pdo->prepare('UPDATE contacts SET label = ?, value = ?, confirmed = ?, sort_order = ? WHERE id = ?')
                ->execute([trim((string) ($row['label'] ?? '')) ?: 'Contacto', $value, isset($row['confirmed']) ? 1 : 0, (int) ($row['sort_order'] ?? 0), (int) $cid]);
        }
        $new = $_POST['new_contact'] ?? [];
        if (trim((string) ($new['value'] ?? '')) !== '') {
            $type = ($new['type'] ?? '') === 'email' ? 'email' : 'phone';
            $pdo->prepare('INSERT INTO contacts (type, label, value, confirmed, sort_order) VALUES (?, ?, ?, ?, 99)')
                ->execute([$type, trim((string) ($new['label'] ?? '')) ?: ($type === 'email' ? 'Correo' : 'Teléfono'), trim((string) $new['value']), isset($new['confirmed']) ? 1 : 0]);
        }
        flash('Contactos guardados.');
    } elseif ($section === 'stats') {
        foreach ((array) ($_POST['stat'] ?? []) as $sid => $row) {
            if (isset($row['delete']) || trim((string) ($row['value'] ?? '')) === '') {
                $pdo->prepare('DELETE FROM stats WHERE id = ?')->execute([(int) $sid]);
                continue;
            }
            $pdo->prepare('UPDATE stats SET value = ?, label = ?, confirmed = ?, sort_order = ? WHERE id = ?')
                ->execute([trim(mb_substr((string) $row['value'], 0, 32)), trim(mb_substr((string) ($row['label'] ?? ''), 0, 120)), isset($row['confirmed']) ? 1 : 0, (int) ($row['sort_order'] ?? 0), (int) $sid]);
        }
        $new = $_POST['new_stat'] ?? [];
        if (trim((string) ($new['value'] ?? '')) !== '') {
            $pdo->prepare('INSERT INTO stats (value, label, confirmed, sort_order) VALUES (?, ?, ?, 99)')
                ->execute([trim(mb_substr((string) $new['value'], 0, 32)), trim(mb_substr((string) ($new['label'] ?? ''), 0, 120)), isset($new['confirmed']) ? 1 : 0]);
        }
        flash('Cifras guardadas.');
    } elseif ($section === 'images') {
        $save = $pdo->prepare('INSERT INTO images (slot, url, alt) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE url = VALUES(url), alt = VALUES(alt)');
        try {
            foreach (array_keys($imageLabels) as $slot) {
                $url = trim((string) ($_POST['img'][$slot]['url'] ?? ''));
                $url = save_upload('file_' . $slot) ?? $url;
                if ($url !== '') {
                    $save->execute([$slot, $url, trim(mb_substr((string) ($_POST['img'][$slot]['alt'] ?? ''), 0, 190))]);
                }
            }
            flash('Imágenes guardadas.');
        } catch (RuntimeException $exception) {
            flash($exception->getMessage(), 'error');
        }
    } elseif ($section === 'brand') {
        $save = $pdo->prepare('INSERT INTO settings (setting_key, setting_value, confirmed) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        try {
            foreach (['logo', 'logo_dark'] as $key) {
                if (isset($_POST['remove_' . $key])) {
                    $save->execute([$key, '']);
                } elseif (($path = save_upload('file_' . $key)) !== null) {
                    $save->execute([$key, $path]);
                }
            }
            foreach (BRAND_DEFAULTS as $key => $default) {
                $value = isset($_POST['reset_colors']) ? '' : (valid_hex((string) ($_POST[$key] ?? '')) ?? '');
                $save->execute([$key, $value === $default ? '' : $value]);
            }
            flash('Logo y colores guardados.');
        } catch (RuntimeException $exception) {
            flash($exception->getMessage(), 'error');
        }
    } elseif ($section === 'password') {
        $statement = $pdo->prepare('SELECT password_hash FROM admins WHERE id = ?');
        $statement->execute([admin_user()['id']]);
        $hash = (string) $statement->fetchColumn();
        $new = (string) ($_POST['new_password'] ?? '');
        if (!password_verify((string) ($_POST['current_password'] ?? ''), $hash)) {
            flash('La contraseña actual no es correcta.', 'error');
        } elseif (mb_strlen($new) < 10) {
            flash('La nueva contraseña debe tener al menos 10 caracteres.', 'error');
        } else {
            $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), admin_user()['id']]);
            flash('Contraseña actualizada.');
        }
    }
    redirect('ajustes.php#' . $section);
}

$site = site_data();
$contacts = $pdo->query('SELECT * FROM contacts ORDER BY sort_order, id')->fetchAll();
$stats = $pdo->query('SELECT * FROM stats ORDER BY sort_order, id')->fetchAll();

admin_header('Datos del sitio', 'ajustes');
?>
<div class="a-head"><h1>Datos del sitio</h1></div>
<p class="hint">Lo que marques como <b>confirmado</b> se muestra normal en el sitio. Lo que no, aparece con la etiqueta «por confirmar» y sin enlaces.</p>

<form id="settings" class="panel" method="post">
  <h2>Datos generales</h2>
  <?= csrf_field() ?><input type="hidden" name="section" value="settings">
  <div class="a-grid-2">
    <?php foreach ($settingFields as $key => [$label, $placeholder, $confirmable]):
        $current = $site['settings'][$key] ?? ['value' => '', 'confirmed' => 0]; ?>
      <div>
        <label><?= e($label) ?> <input name="s[<?= $key ?>]" value="<?= e($current['value']) ?>" placeholder="<?= e($placeholder) ?>"></label>
        <?php if ($confirmable): ?><label class="a-check"><input type="checkbox" name="c[<?= $key ?>]" <?= (int) $current['confirmed'] === 1 ? 'checked' : '' ?>> Confirmado</label><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <label class="a-check"><input type="checkbox" name="catalog_sample" <?= setting('catalog_sample') === '1' ? 'checked' : '' ?>> Mostrar en la tienda el aviso «Catálogo de demostración»</label>
  <button class="a-btn a-btn-primary" type="submit">Guardar datos</button>
</form>

<?php $brand = brand_colors(); ?>
<form id="brand" class="panel" method="post" enctype="multipart/form-data">
  <h2>Logo y colores</h2>
  <p class="hint">Sube el logo en PNG o WEBP con fondo transparente (ideal: horizontal, unos 400 × 120 px). Si no subes ninguno, se usa el logo de texto «RedTech».</p>
  <?= csrf_field() ?><input type="hidden" name="section" value="brand">
  <div class="a-images">
    <?php foreach (['logo' => 'Logo (para fondo blanco)', 'logo_dark' => 'Logo para fondo oscuro (opcional, versión blanca)'] as $key => $label): $current = setting($key); ?>
      <fieldset class="a-image">
        <legend><?= e($label) ?></legend>
        <div class="a-logo-preview<?= $key === 'logo_dark' ? ' is-dark' : '' ?>">
          <img id="prev-<?= $key ?>" src="<?= $current !== '' ? e(url($current)) : '' ?>" alt=""<?= $current === '' ? ' hidden' : '' ?>>
          <?php if ($current === ''): ?><span>Sin logo</span><?php endif; ?>
        </div>
        <div>
          <label>Subir <input type="file" name="file_<?= $key ?>" accept="image/png,image/webp,image/jpeg" data-preview="#prev-<?= $key ?>"></label>
          <?php if ($current !== ''): ?><label class="a-check"><input type="checkbox" name="remove_<?= $key ?>"> Quitar este logo</label><?php endif; ?>
        </div>
      </fieldset>
    <?php endforeach; ?>
  </div>
  <p class="hint">Sin logo para fondo oscuro, en las zonas oscuras el logo se muestra sobre una placa blanca.</p>
  <div class="a-colors">
    <label>Color principal <span class="hint">botones, enlaces y degradado</span><input type="color" name="color_primary" value="<?= e($brand['color_primary']) ?>"></label>
    <label>Color de acento <span class="hint">segundo color del degradado y detalles</span><input type="color" name="color_accent" value="<?= e($brand['color_accent']) ?>"></label>
    <label>Color oscuro <span class="hint">fondos de portada, secciones oscuras y pie</span><input type="color" name="color_dark" value="<?= e($brand['color_dark']) ?>"></label>
  </div>
  <label class="a-check"><input type="checkbox" name="reset_colors"> Volver a los colores originales</label>
  <button class="a-btn a-btn-primary" type="submit">Guardar logo y colores</button>
</form>

<form id="contacts" class="panel" method="post">
  <h2>Teléfonos y correos</h2>
  <?= csrf_field() ?><input type="hidden" name="section" value="contacts">
  <table class="a-table a-table-edit">
    <thead><tr><th>Tipo</th><th>Etiqueta</th><th>Valor</th><th>Confirmado</th><th>Orden</th><th>Quitar</th></tr></thead>
    <tbody>
      <?php foreach ($contacts as $c): ?>
        <tr>
          <td data-label="Tipo"><?= $c['type'] === 'phone' ? 'Teléfono' : 'Correo' ?></td>
          <td data-label="Etiqueta"><input name="contact[<?= $c['id'] ?>][label]" value="<?= e($c['label']) ?>" aria-label="Etiqueta"></td>
          <td data-label="Valor"><input name="contact[<?= $c['id'] ?>][value]" value="<?= e($c['value']) ?>" aria-label="Valor"></td>
          <td data-label="Confirmado"><input type="checkbox" name="contact[<?= $c['id'] ?>][confirmed]" <?= (int) $c['confirmed'] === 1 ? 'checked' : '' ?> aria-label="Confirmado"></td>
          <td data-label="Orden"><input type="number" name="contact[<?= $c['id'] ?>][sort_order]" value="<?= (int) $c['sort_order'] ?>" aria-label="Orden" class="a-num"></td>
          <td data-label="Quitar"><input type="checkbox" name="contact[<?= $c['id'] ?>][delete]" aria-label="Quitar"></td>
        </tr>
      <?php endforeach; ?>
      <tr class="a-new">
        <td data-label="Tipo"><select name="new_contact[type]" aria-label="Tipo"><option value="phone">Teléfono</option><option value="email">Correo</option></select></td>
        <td data-label="Etiqueta"><input name="new_contact[label]" placeholder="Ej.: Ventas" aria-label="Etiqueta nueva"></td>
        <td data-label="Valor"><input name="new_contact[value]" placeholder="Agregar nuevo" aria-label="Valor nuevo"></td>
        <td data-label="Confirmado"><input type="checkbox" name="new_contact[confirmed]" checked aria-label="Confirmado"></td>
        <td></td><td></td>
      </tr>
    </tbody>
  </table>
  <button class="a-btn a-btn-primary" type="submit">Guardar contactos</button>
</form>

<form id="stats" class="panel" method="post">
  <h2>Cifras de la empresa</h2>
  <p class="hint">Se animan en el inicio. Usa valores como <code>+150</code>, <code>24/7</code> o <code>100%</code>.</p>
  <?= csrf_field() ?><input type="hidden" name="section" value="stats">
  <table class="a-table a-table-edit">
    <thead><tr><th>Valor</th><th>Texto</th><th>Confirmado</th><th>Orden</th><th>Quitar</th></tr></thead>
    <tbody>
      <?php foreach ($stats as $s): ?>
        <tr>
          <td data-label="Valor"><input name="stat[<?= $s['id'] ?>][value]" value="<?= e($s['value']) ?>" class="a-num" aria-label="Valor"></td>
          <td data-label="Texto"><input name="stat[<?= $s['id'] ?>][label]" value="<?= e($s['label']) ?>" aria-label="Texto"></td>
          <td data-label="Confirmado"><input type="checkbox" name="stat[<?= $s['id'] ?>][confirmed]" <?= (int) $s['confirmed'] === 1 ? 'checked' : '' ?> aria-label="Confirmado"></td>
          <td data-label="Orden"><input type="number" name="stat[<?= $s['id'] ?>][sort_order]" value="<?= (int) $s['sort_order'] ?>" class="a-num" aria-label="Orden"></td>
          <td data-label="Quitar"><input type="checkbox" name="stat[<?= $s['id'] ?>][delete]" aria-label="Quitar"></td>
        </tr>
      <?php endforeach; ?>
      <tr class="a-new">
        <td data-label="Valor"><input name="new_stat[value]" placeholder="+50" class="a-num" aria-label="Valor nuevo"></td>
        <td data-label="Texto"><input name="new_stat[label]" placeholder="clientes atendidos" aria-label="Texto nuevo"></td>
        <td data-label="Confirmado"><input type="checkbox" name="new_stat[confirmed]" checked aria-label="Confirmado"></td>
        <td></td><td></td>
      </tr>
    </tbody>
  </table>
  <button class="a-btn a-btn-primary" type="submit">Guardar cifras</button>
</form>

<form id="images" class="panel" method="post" enctype="multipart/form-data">
  <h2>Fotos del sitio</h2>
  <?= csrf_field() ?><input type="hidden" name="section" value="images">
  <div class="a-images">
    <?php foreach ($imageLabels as $slot => $label): $img = $site['images'][$slot] ?? ['url' => '', 'alt' => '']; ?>
      <fieldset class="a-image">
        <legend><?= e($label) ?></legend>
        <img id="img-<?= $slot ?>" src="<?= e(image_src($img['url'], 320, 200)) ?>" alt="" width="160" height="100" loading="lazy">
        <div>
          <label>Subir <input type="file" name="file_<?= $slot ?>" accept="image/jpeg,image/png,image/webp" data-preview="#img-<?= $slot ?>"></label>
          <label>Dirección <input name="img[<?= $slot ?>][url]" value="<?= e($img['url']) ?>"></label>
          <label>Descripción (para accesibilidad) <input name="img[<?= $slot ?>][alt]" value="<?= e($img['alt']) ?>"></label>
        </div>
      </fieldset>
    <?php endforeach; ?>
  </div>
  <button class="a-btn a-btn-primary" type="submit">Guardar fotos</button>
</form>

<form id="password" class="panel" method="post">
  <h2>Cambiar mi contraseña</h2>
  <?= csrf_field() ?><input type="hidden" name="section" value="password">
  <div class="a-grid-2">
    <label>Contraseña actual <input type="password" name="current_password" required autocomplete="current-password"></label>
    <label>Nueva contraseña <input type="password" name="new_password" required minlength="10" autocomplete="new-password"></label>
  </div>
  <button class="a-btn" type="submit">Cambiar contraseña</button>
</form>
<?php admin_footer(); ?>
