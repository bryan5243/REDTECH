<?php
$pending = '<span class="pending">por confirmar</span>';
$textsPending = empty($content['texts_confirmed']) ? '<span class="pending">textos por confirmar</span>' : '';
$images = $site['images'];

$confirmedPhone = null;

foreach ($site['contacts'] as $contact) {
    $confirmed = (int) $contact['confirmed'] === 1;
    if ($confirmed && $contact['type'] === 'phone' && $confirmedPhone === null) {
        $confirmedPhone = preg_replace('/[^0-9]/', '', $contact['value']);
    }
}

$interestOptions = static function (string $selected) use ($interests): string {
    $html = '';
    foreach ($interests as $value => $label) {
        $html .= '<option value="' . e($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    return $html;
};

$audience = static function (string $label) use ($para): void { ?>
  <div class="audience" role="group" aria-label="<?= e($label) ?>">
    <a href="?para=empresa" data-para="empresa"<?= $para === 'empresa' ? ' aria-current="true"' : '' ?>>Empresa</a>
    <a href="?para=hogar" data-para="hogar"<?= $para === 'hogar' ? ' aria-current="true"' : '' ?>>Hogar</a>
  </div>
<?php };

$navLinks = ['#servicios' => 'Servicios', '#nosotros' => 'Nosotros', '#equipos' => 'Equipos', '#preguntas' => 'Preguntas', '#contacto' => 'Contacto'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RedTech | Cableado estructurado, cámaras de seguridad y servidores</title>
  <meta name="description" content="Cableado estructurado, cámaras de seguridad y servidores para empresas y hogares. Pide tu cotización a RedTech.">
  <meta name="theme-color" content="#0b2239">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://images.unsplash.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700&family=Source+Sans+3:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="assets/site.css">
  <script src="assets/site.js" defer></script>
</head>
<body data-para="<?= e($para) ?>">
<a class="skip" href="#principal">Saltar al contenido</a>

<header class="topbar">
  <div class="wrap topbar-inner">
    <a class="wordmark" href="./"><span>Red</span>Tech</a>
    <nav class="nav-desktop" aria-label="Principal">
      <?php foreach ($navLinks as $href => $label): ?><a href="<?= $href ?>"><?= $label ?></a><?php endforeach; ?>
    </nav>
    <a class="btn btn-primary topbar-cta" href="#contacto">Cotizar</a>
    <details class="nav-menu">
      <summary>Menú</summary>
      <nav aria-label="Principal, móvil">
        <?php foreach ($navLinks as $href => $label): ?><a href="<?= $href ?>"><?= $label ?></a><?php endforeach; ?>
      </nav>
    </details>
  </div>
</header>

<main id="principal">
  <section class="hero" aria-labelledby="titulo">
    <div class="hero-photo"><?= picture($images['hero'], '100vw', 1800, 1000, true) ?></div>
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <h1 id="titulo">Infraestructura tecnológica para tu <span class="flip-word" data-flip data-empresa="empresa" data-hogar="hogar"><?= e($para) ?></span></h1>
        <p class="hero-lead" data-flip data-empresa="<?= e($content['lead']['empresa']) ?>" data-hogar="<?= e($content['lead']['hogar']) ?>"><?= e($content['lead'][$para]) ?></p>
        <a class="link-arrow" href="#servicios">Ver servicios <?= icon('flecha') ?></a>
      </div>

      <form class="quick" method="post" action="#contacto" aria-labelledby="pedir">
        <h2 id="pedir">Cotización para tu <span data-flip data-empresa="empresa" data-hogar="hogar"><?= e($para) ?></span></h2>
        <?php $audience('¿Para quién es la cotización?'); ?>
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
        <button class="btn btn-primary" type="submit">Enviar solicitud</button>
      </form>
    </div>
  </section>

  <?php if ($site['stats'] !== []): ?>
    <section class="wrap stats-wrap" aria-label="Datos de RedTech">
      <dl class="stats">
        <?php foreach ($site['stats'] as $i => $stat): ?>
          <div class="reveal" style="--i:<?= $i ?>">
            <dt><?= e($stat['label']) ?><?= (int) $stat['confirmed'] === 1 ? '' : ' ' . $pending ?></dt>
            <dd data-count><?= e($stat['value']) ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </section>
  <?php endif; ?>

  <section id="servicios" class="section" aria-labelledby="servicios-titulo">
    <div class="wrap">
      <div class="section-head section-head-split">
        <div class="reveal">
          <h2 id="servicios-titulo">Servicios</h2>
          <p>Soluciones de red, seguridad y almacenamiento. <?= $textsPending ?></p>
        </div>
        <div class="reveal" style="--i:1"><?php $audience('Ver servicios para'); ?></div>
      </div>
      <div class="cards">
        <?php $i = 0; foreach ($content['services'] as $key => $service): ?>
          <article id="<?= e($key) ?>" class="card reveal" style="--i:<?= $i++ ?>">
            <div class="card-photo"><?= picture($images[$key], '(min-width: 1000px) 380px, (min-width: 761px) 45vw, 100vw', 800, 500) ?></div>
            <div class="card-body">
              <h3><?= e($service['name']) ?></h3>
              <p class="card-text"><?= e($service['detail']) ?></p>
              <p class="case case-empresa"><strong>En empresas:</strong> <?= e($service['empresa_detail']) ?></p>
              <p class="case case-hogar"><strong>En hogares:</strong> <?= e($service['hogar_detail']) ?></p>
              <a class="link-arrow" href="?para=<?= e($para) ?>&amp;interes=<?= e($key) ?>#contacto" data-pick="<?= e($key) ?>">Cotizar <span class="sr-only"><?= e(mb_strtolower($service['name'])) ?></span> <?= icon('flecha') ?></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="nosotros" class="section section-tint" aria-labelledby="nosotros-titulo">
    <div class="wrap split">
      <div class="split-photo reveal"><?= picture($images['nosotros'], '(min-width: 1000px) 560px, 100vw', 1000, 800) ?></div>
      <div>
        <div class="section-head reveal">
          <h2 id="nosotros-titulo">Por qué elegir RedTech</h2>
          <p><?= $textsPending ?></p>
        </div>
        <ul class="reasons">
          <?php foreach ($content['reasons'] as $i => $reason): ?>
            <li class="reveal" style="--i:<?= $i ?>">
              <span class="reason-mark" aria-hidden="true"><?= icon('check') ?></span>
              <div><h3><?= e($reason['title']) ?></h3><p><?= e($reason['text']) ?></p></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <section id="proceso" class="section" aria-labelledby="proceso-titulo">
    <div class="wrap">
      <div class="section-head reveal">
        <h2 id="proceso-titulo">Cómo trabajamos</h2>
        <p><?= $textsPending ?></p>
      </div>
      <ol class="steps">
        <?php foreach ($content['steps'] as $i => $step): ?>
          <li class="reveal" style="--i:<?= $i ?>">
            <span class="step-number" aria-hidden="true"><?= $i + 1 ?></span>
            <h3><?= e($step['title']) ?></h3>
            <p><?= e($step['text']) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="section section-tint" aria-labelledby="sectores-titulo">
    <div class="wrap">
      <div class="section-head reveal">
        <h2 id="sectores-titulo">A quién atendemos</h2>
        <p><?= $textsPending ?></p>
      </div>
      <div class="sectors">
        <?php $i = 0; foreach ($content['sectors'] as $slot => $sector): ?>
          <article class="sector reveal" style="--i:<?= $i++ ?>">
            <?= picture($images[$slot], '(min-width: 761px) 600px, 100vw', 1000, 560) ?>
            <div class="sector-body">
              <h3><?= e($sector['name']) ?></h3>
              <ul>
                <?php foreach ($sector['items'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?>
              </ul>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="equipos" class="section" aria-labelledby="equipos-titulo">
    <div class="wrap">
      <div class="section-head reveal">
        <h2 id="equipos-titulo">Equipos</h2>
        <p>Estamos preparando el catálogo en línea. Mientras tanto, dinos qué equipo buscas y te responderemos.</p>
      </div>
      <ul class="products">
        <?php $i = 0; foreach ($content['products'] as $key => $product): ?>
          <li class="product reveal" style="--i:<?= $i++ ?>">
            <div class="product-photo"><?= picture($images['producto_' . $key], '(min-width: 1000px) 285px, (min-width: 480px) 45vw, 100vw', 600, 450) ?></div>
            <div class="product-body">
              <h3><?= e($product['name']) ?></h3>
              <p><?= e($product['text']) ?></p>
              <a class="btn btn-outline" href="?para=<?= e($para) ?>&amp;interes=tienda&amp;producto=<?= e($key) ?>#contacto" data-pick="tienda" data-product="<?= e($product['name']) ?>">Cotizar <span class="sr-only"><?= e(mb_strtolower($product['name'])) ?></span></a>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="image-note">Fotografías ilustrativas.</p>
    </div>
  </section>

  <?php if (!empty($content['show_placeholders'])): ?>
    <section class="section section-tint" aria-label="Espacios por completar">
      <div class="wrap placeholders">
        <div class="placeholder reveal">
          <h2>Certificaciones</h2>
          <p><span class="pending">por añadir</span> Aquí se mostrarán las certificaciones reales de RedTech.</p>
        </div>
        <div class="placeholder reveal" style="--i:1">
          <h2>Testimonios</h2>
          <p><span class="pending">por añadir</span> Aquí se mostrarán opiniones reales de tus clientes.</p>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section id="preguntas" class="section" aria-labelledby="preguntas-titulo">
    <div class="wrap faq-wrap">
      <div class="section-head reveal">
        <h2 id="preguntas-titulo">Preguntas frecuentes</h2>
        <p><?= $textsPending ?></p>
      </div>
      <div class="faq reveal" style="--i:1">
        <?php foreach ($content['faq'] as $item): ?>
          <details>
            <summary><?= e($item['q']) ?><?= icon('flecha') ?></summary>
            <p><?= e($item['a']) ?></p>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="contacto" class="contact" aria-labelledby="contacto-titulo">
    <div class="wrap contact-grid">
      <div class="contact-info">
        <h2 id="contacto-titulo" class="reveal">Hablemos de tu proyecto</h2>
        <p class="reveal" style="--i:1">Te contactamos para resolver tus dudas y preparar tu cotización.</p>
        <dl class="reveal" style="--i:2">
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
            <dd>
              <?= e($location['value']) ?><?= (int) $location['confirmed'] === 1 ? '' : ' ' . $pending ?>
              <?php if (($site['settings']['map_url']['value'] ?? '') !== ''): ?>
                <a class="map-link" href="<?= e($site['settings']['map_url']['value']) ?>">Ver en el mapa</a>
              <?php endif; ?>
            </dd>
          </div>
        </dl>
      </div>

      <?php if ($sent): ?>
        <div class="full full-sent">
          <p class="notice" role="status" tabindex="-1" data-focus>Recibimos tu solicitud. Te contactaremos pronto.</p>
          <a class="link-arrow" href="?para=<?= e($para) ?>#contacto">Enviar otra solicitud <?= icon('flecha') ?></a>
        </div>
      <?php else: ?>
        <form class="full" method="post" action="#contacto" novalidate>
          <?php if (isset($errors['form'])): ?>
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

          <button class="btn btn-primary" type="submit">Enviar solicitud</button>
        </form>
      <?php endif; ?>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="wrap footer-grid">
    <div>
      <span class="wordmark"><span>Red</span>Tech</span>
      <p>Cableado estructurado, cámaras de seguridad y servidores para empresas y hogares.</p>
    </div>
    <nav aria-label="Pie de página">
      <h2>Navegación</h2>
      <?php foreach ($navLinks as $href => $label): ?><a href="<?= $href ?>"><?= $label ?></a><?php endforeach; ?>
    </nav>
    <div>
      <h2>Contacto</h2>
      <?php foreach ($site['contacts'] as $contact): ?>
        <p><?= e($contact['value']) ?><?= (int) $contact['confirmed'] === 1 ? '' : ' ' . $pending ?></p>
      <?php endforeach; ?>
      <p><?= e($location['value']) ?><?= (int) $location['confirmed'] === 1 ? '' : ' ' . $pending ?></p>
      <?php foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram'] as $key => $label): ?>
        <?php if (($site['settings'][$key]['value'] ?? '') !== ''): ?>
          <a href="<?= e($site['settings'][$key]['value']) ?>"><?= $label ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <p class="wrap footer-legal">© <?= date('Y') ?> RedTech</p>
</footer>

<?php if ($confirmedPhone !== null): ?>
  <a class="whatsapp" href="https://wa.me/<?= e($confirmedPhone) ?>" aria-label="Escríbenos por WhatsApp"><?= icon('chat') ?><span>WhatsApp</span></a>
<?php endif; ?>
</body>
</html>
