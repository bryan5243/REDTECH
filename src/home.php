<?php
$pending = '<span class="pending">por confirmar</span>';

$interestOptions = static function (string $selected) use ($interests): string {
    $html = '';
    foreach ($interests as $value => $label) {
        $html .= '<option value="' . e($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    return $html;
};
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RedTech | Cableado estructurado, cámaras de seguridad y servidores</title>
  <meta name="description" content="Cableado estructurado, cámaras de seguridad y servidores para empresas y hogares. Pide tu cotización a RedTech.">
  <meta name="theme-color" content="#14130e">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&family=Barlow:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="assets/site.css">
  <script src="assets/site.js" defer></script>
</head>
<body data-para="<?= e($para) ?>">
<a class="skip" href="#principal">Saltar al contenido</a>

<header class="topbar">
  <a class="wordmark" href="./">RedTech</a>
  <nav aria-label="Principal">
    <a href="#servicios">Servicios</a>
    <a href="#tienda">Tienda</a>
    <a href="#nosotros">Nosotros</a>
    <a href="#contacto">Contacto</a>
  </nav>
  <a class="topbar-cta" href="#contacto">Cotizar</a>
</header>

<main id="principal">
  <section class="board" aria-labelledby="titulo">
    <div class="plate plate-intro">
      <div>
        <h1 id="titulo">Conectamos y protegemos tu <span data-flip data-empresa="empresa" data-hogar="hogar"><?= e($para) ?></span>.</h1>
        <p>Cableado estructurado, cámaras de seguridad y servidores para empresas y hogares<?= (int) $location['confirmed'] === 1 ? ', desde ' . e($location['value']) : '' ?>.</p>
      </div>
      <div class="audience" role="group" aria-label="¿Para quién es?">
        <a href="?para=empresa" data-para="empresa"<?= $para === 'empresa' ? ' aria-current="true"' : '' ?>>Empresa</a>
        <a href="?para=hogar" data-para="hogar"<?= $para === 'hogar' ? ' aria-current="true"' : '' ?>>Hogar</a>
      </div>
    </div>

    <?php foreach ($content['services'] as $key => $service): ?>
      <a class="plate row row-<?= e($key) ?>" href="#<?= e($key) ?>">
        <span class="row-icon"><?= icon($key) ?></span>
        <span class="row-name"><?= e($service['name']) ?></span>
        <span class="row-text" data-flip data-empresa="<?= e($service['empresa']) ?>" data-hogar="<?= e($service['hogar']) ?>"><?= e($service[$para]) ?></span>
        <span class="row-arrow"><?= icon('flecha') ?></span>
      </a>
    <?php endforeach; ?>

    <a class="plate row row-tienda" href="#tienda">
      <span class="row-icon"><?= icon('tienda') ?></span>
      <span class="row-name">Tienda</span>
      <span class="row-text">Cables, servidores, routers y cámaras.</span>
      <span class="row-arrow"><?= icon('flecha') ?></span>
    </a>

    <form class="plate quick" method="post" action="#contacto" aria-labelledby="pedir">
      <h2 id="pedir">Pide tu cotización</h2>
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <input type="hidden" name="para" value="<?= e($para) ?>" data-para-input>
      <div class="hp" aria-hidden="true"><label>No llenar <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <label>Nombre <input name="name" required maxlength="120" autocomplete="name"></label>
      <label>Teléfono <input name="phone" type="tel" required maxlength="40" autocomplete="tel"></label>
      <label>Necesito
        <select name="interest" required data-interest>
          <option value="">Elegir</option>
          <?= $interestOptions($selected) ?>
        </select>
      </label>
      <button type="submit">Enviar solicitud</button>
    </form>
  </section>

  <section id="servicios" class="services" aria-label="Servicios">
    <?php foreach ($content['services'] as $key => $service): ?>
      <article id="<?= e($key) ?>" class="plate band band-<?= e($key) ?>">
        <div class="band-mark"><?= icon($key) ?></div>
        <div class="band-main">
          <h2><?= e($service['name']) ?></h2>
          <p><?= e($service['detail']) ?></p>
        </div>
        <dl class="band-cases">
          <div class="case case-empresa"><dt>En empresas</dt><dd><?= e($service['empresa_detail']) ?></dd></div>
          <div class="case case-hogar"><dt>En hogares</dt><dd><?= e($service['hogar_detail']) ?></dd></div>
        </dl>
        <a class="band-link" href="?para=<?= e($para) ?>&amp;interes=<?= e($key) ?>#contacto" data-pick="<?= e($key) ?>">Cotizar <?= e(mb_strtolower($service['name'])) ?> <?= icon('flecha') ?></a>
      </article>
    <?php endforeach; ?>
  </section>

  <section id="tienda" class="plate store" aria-labelledby="tienda-titulo">
    <div class="store-head">
      <h2 id="tienda-titulo">Tienda</h2>
      <p>Estamos preparando el catálogo en línea. Mientras tanto, dinos qué equipo buscas y te responderemos.</p>
      <a class="store-link" href="?para=<?= e($para) ?>&amp;interes=tienda#contacto" data-pick="tienda">Consultar un producto <?= icon('flecha') ?></a>
    </div>
    <ul class="store-list">
      <?php foreach ($content['products'] as $key => $name): ?>
        <li>
          <a href="?para=<?= e($para) ?>&amp;interes=tienda&amp;producto=<?= e($key) ?>#contacto" data-pick="tienda" data-product="<?= e($name) ?>">
            <?= icon($key === 'cables' ? 'cableado' : $key) ?><span><?= e($name) ?></span><?= icon('flecha') ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section id="nosotros" class="plate company" aria-labelledby="nosotros-titulo">
    <div class="company-text">
      <h2 id="nosotros-titulo">Red, cámaras y servidores en un mismo lugar</h2>
      <p>RedTech ofrece cableado estructurado, cámaras de seguridad y servidores, además de los equipos para cada uno.</p>
    </div>
    <?php if ($site['stats'] !== []): ?>
      <dl class="stats">
        <?php foreach ($site['stats'] as $stat): ?>
          <div>
            <dt><?= e($stat['label']) ?><?= (int) $stat['confirmed'] === 1 ? '' : ' ' . $pending ?></dt>
            <dd><?= e($stat['value']) ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    <?php endif; ?>
  </section>

  <section id="contacto" class="plate contact" aria-labelledby="contacto-titulo">
    <div class="contact-info">
      <h2 id="contacto-titulo">Cuéntanos qué necesitas</h2>
      <p>Te contactamos para resolver tus dudas y preparar tu cotización.</p>
      <dl>
        <?php foreach ($site['contacts'] as $contact):
            $confirmed = (int) $contact['confirmed'] === 1;
            $href = $contact['type'] === 'phone'
                ? 'tel:' . preg_replace('/[^0-9+]/', '', $contact['value'])
                : 'mailto:' . $contact['value'];
        ?>
          <div>
            <dt><?= e($contact['label']) ?></dt>
            <dd>
              <?php if ($confirmed): ?>
                <a href="<?= e($href) ?>"><?= e($contact['value']) ?></a>
              <?php else: ?>
                <?= e($contact['value']) ?> <?= $pending ?>
              <?php endif; ?>
            </dd>
          </div>
        <?php endforeach; ?>
        <div>
          <dt>Ubicación</dt>
          <dd><?= e($location['value']) ?><?= (int) $location['confirmed'] === 1 ? '' : ' ' . $pending ?></dd>
        </div>
      </dl>
    </div>

    <form class="full" method="post" action="#contacto" novalidate>
      <?php if ($sent): ?>
        <p class="notice" role="status" tabindex="-1" data-focus>Recibimos tu solicitud. Te contactaremos pronto.</p>
      <?php elseif (isset($errors['form'])): ?>
        <p class="alert" role="alert" tabindex="-1" data-focus><?= e($errors['form']) ?></p>
      <?php elseif ($errors !== []): ?>
        <p class="alert" role="alert" tabindex="-1" data-focus>Revisa los campos marcados para enviar tu solicitud.</p>
      <?php endif; ?>

      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <input type="hidden" name="para" value="<?= e($para) ?>" data-para-input>
      <div class="hp" aria-hidden="true"><label>No llenar <input name="website" tabindex="-1" autocomplete="off"></label></div>

      <?php
      $field = static function (string $name, string $label, string $type, string $autocomplete) use ($old, $errors): void {
          $error = $errors[$name] ?? null; ?>
          <div class="field field-<?= $name ?><?= $error ? ' has-error' : '' ?>">
            <label for="f-<?= $name ?>"><?= $label ?></label>
            <input id="f-<?= $name ?>" name="<?= $name ?>" type="<?= $type ?>" autocomplete="<?= $autocomplete ?>" value="<?= e($old[$name] ?? '') ?>"<?= $error ? ' aria-invalid="true" aria-describedby="e-' . $name . '"' : '' ?>>
            <?php if ($error): ?><p class="error" id="e-<?= $name ?>"><?= e($error) ?></p><?php endif; ?>
          </div>
      <?php };
      $field('name', 'Nombre', 'text', 'name');
      $field('phone', 'Teléfono', 'tel', 'tel');
      $field('email', 'Correo', 'email', 'email');
      $field('company', 'Empresa (opcional)', 'text', 'organization');
      ?>

      <div class="field field-wide<?= isset($errors['interest']) ? ' has-error' : '' ?>">
        <label for="f-interest">Necesito</label>
        <select id="f-interest" name="interest" data-interest<?= isset($errors['interest']) ? ' aria-invalid="true" aria-describedby="e-interest"' : '' ?>>
          <option value="">Elegir</option>
          <?= $interestOptions($selected) ?>
        </select>
        <?php if (isset($errors['interest'])): ?><p class="error" id="e-interest"><?= e($errors['interest']) ?></p><?php endif; ?>
      </div>

      <div class="field field-wide<?= isset($errors['message']) ? ' has-error' : '' ?>">
        <label for="f-message">Mensaje (opcional)</label>
        <textarea id="f-message" name="message" rows="4"<?= isset($errors['message']) ? ' aria-invalid="true" aria-describedby="e-message"' : '' ?>><?= e($old['message'] ?? $prefilledMessage) ?></textarea>
        <?php if (isset($errors['message'])): ?><p class="error" id="e-message"><?= e($errors['message']) ?></p><?php endif; ?>
      </div>

      <button type="submit">Enviar solicitud</button>
    </form>
  </section>
</main>

<footer class="footer">
  <span class="wordmark">RedTech</span>
  <span>Cableado estructurado · Cámaras de seguridad · Servidores</span>
  <span>© <?= date('Y') ?> RedTech</span>
</footer>
</body>
</html>
