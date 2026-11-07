<?php
$pending = '<span class="pending">por confirmar</span>';
$images = $site['images'];
$hero = $content['hero'];
$waQuote = whatsapp_link('Hola RedTech, quiero una cotización.');

$interestOptions = static function (string $selected) use ($interests): string {
    $html = '';
    foreach ($interests as $value => $label) {
        $html .= '<option value="' . e($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    return $html;
};

page_header(
    'RedTech | Cableado estructurado, cámaras de seguridad y servidores',
    'Cableado estructurado, cámaras de seguridad, servidores y tienda de equipos de red. Pide tu cotización o compra por WhatsApp.',
    'inicio',
    'page-home'
);
?>
<main id="principal">
  <section class="hero" aria-labelledby="titulo">
    <div class="hero-bg" aria-hidden="true">
      <?= picture($images['hero'], '100vw', 1900, 1100, true) ?>
      <canvas class="hero-net" data-network></canvas>
      <div class="hero-grid-lines"></div>
    </div>
    <div class="wrap hero-inner">
      <div class="hero-copy">
        <p class="eyebrow hero-in" style="--d:0"><span class="pulse-dot" aria-hidden="true"></span><?= e($hero['eyebrow']) ?> · <?= e(explode(',', $site['settings']['location']['value'])[0]) ?></p>
        <h1 id="titulo" class="hero-title">
          <span class="hero-in" style="--d:1"><?= e($hero['title_before']) ?></span>
          <span class="rotator hero-in" style="--d:2" data-rotator="<?= e(json_encode($hero['rotating'], JSON_UNESCAPED_UNICODE)) ?>"><span class="rotator-word"><?= e($hero['rotating'][0]) ?></span></span>
          <span class="hero-in" style="--d:3"><?= e($hero['title_after']) ?></span>
        </h1>
        <p class="hero-lead hero-in" style="--d:4"><?= e($hero['lead']) ?></p>
        <div class="hero-actions hero-in" style="--d:5">
          <a class="btn btn-primary btn-lg" href="#contacto">Solicitar cotización <?= icon('flecha') ?></a>
          <a class="btn btn-glass btn-lg" href="<?= url('tienda.php') ?>"><?= icon('tienda') ?> Ver tienda</a>
        </div>
        <ul class="hero-chips hero-in" style="--d:6" aria-label="Lo que hacemos">
          <li><?= icon('cableado') ?> Cableado</li>
          <li><?= icon('camaras') ?> Cámaras</li>
          <li><?= icon('servidores') ?> Servidores</li>
          <li><?= icon('wifi') ?> Redes Wi-Fi</li>
        </ul>
      </div>

      <div class="hero-visual hero-in" style="--d:3" aria-hidden="true">
        <div class="hv-card hv-main" data-tilt>
          <?= picture($images['servidores'], '(min-width: 1000px) 460px, 0px', 920, 1100) ?>
          <div class="hv-scan"></div>
        </div>
        <div class="hv-float hv-a"><span class="hv-icon"><?= icon('camaras') ?></span><div><b>Videovigilancia IP</b><small>Acceso desde tu celular</small></div></div>
        <div class="hv-float hv-b"><span class="hv-icon"><?= icon('cableado') ?></span><div><b>Red estructurada</b><small>Ordenada y documentada</small></div></div>
        <div class="hv-float hv-c"><span class="hv-icon hv-icon-ok"><?= icon('check') ?></span><div><b>Instalación lista</b><small>Soporte posterior</small></div></div>
      </div>
    </div>
    <a class="scroll-cue" href="#servicios" aria-label="Bajar a servicios"><span></span></a>
  </section>

  <div class="marquee" aria-hidden="true">
    <div class="marquee-track">
      <?php for ($r = 0; $r < 2; $r++): foreach ($content['marquee'] as $word): ?>
        <span><?= e($word) ?></span>
      <?php endforeach; endfor; ?>
    </div>
  </div>

  <?php if ($site['stats'] !== []): ?>
    <section class="wrap stats-wrap" aria-label="RedTech en cifras">
      <dl class="stats">
        <?php foreach ($site['stats'] as $i => $stat): ?>
          <div class="stat reveal" style="--i:<?= $i ?>">
            <dd data-count="<?= e($stat['value']) ?>"><?= e($stat['value']) ?></dd>
            <dt><?= e($stat['label']) ?><?= (int) $stat['confirmed'] === 1 ? '' : ' ' . $pending ?></dt>
          </div>
        <?php endforeach; ?>
      </dl>
    </section>
  <?php endif; ?>

  <section id="servicios" class="section" aria-labelledby="servicios-titulo">
    <div class="wrap">
      <header class="section-head reveal">
        <p class="kicker">Servicios</p>
        <h2 id="servicios-titulo">Soluciones completas para tu infraestructura</h2>
        <p>Desde el primer cable hasta el servidor: diseñamos, instalamos y damos soporte.</p>
      </header>
      <div class="services">
        <?php $i = 0; foreach ($content['services'] as $key => $service): ?>
          <article id="<?= e($key) ?>" class="service reveal" style="--i:<?= $i++ ?>">
            <div class="service-media"><?= picture($images[$key], '(min-width: 1000px) 400px, (min-width: 700px) 50vw, 100vw', 800, 560) ?></div>
            <div class="service-body">
              <span class="service-icon"><?= icon($key) ?></span>
              <h3><?= e($service['name']) ?></h3>
              <p><?= e($service['detail']) ?></p>
              <ul class="ticks">
                <?php foreach ($service['items'] as $item): ?><li><?= icon('check') ?><?= e($item) ?></li><?php endforeach; ?>
              </ul>
              <a class="link-arrow" href="?interes=<?= e($key) ?>#contacto" data-pick="<?= e($key) ?>">Cotizar <span class="sr-only"><?= e(mb_strtolower($service['name'])) ?></span><?= icon('flecha') ?></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="extras">
        <?php foreach ($content['extras'] as $j => $extra): ?>
          <div class="extra reveal" style="--i:<?= $j ?>">
            <span class="extra-icon"><?= icon($extra['icon']) ?></span>
            <div><h3><?= e($extra['name']) ?></h3><p><?= e($extra['text']) ?></p></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section section-dark shop-teaser" aria-labelledby="tienda-titulo">
    <div class="wrap">
      <header class="section-head section-head-row reveal">
        <div>
          <p class="kicker kicker-light">Tienda en línea</p>
          <h2 id="tienda-titulo">Equipos destacados</h2>
          <p>Agrega al carrito y envía tu pedido por WhatsApp en un clic.</p>
        </div>
        <div class="carousel-nav">
          <button class="icon-btn icon-btn-light" type="button" data-carousel-prev aria-label="Anterior"><?= icon('atras') ?></button>
          <button class="icon-btn icon-btn-light" type="button" data-carousel-next aria-label="Siguiente"><?= icon('flecha') ?></button>
        </div>
      </header>
      <div class="cat-pills reveal">
        <?php foreach ($categories as $category): ?>
          <a class="cat-pill" href="<?= url('tienda.php?categoria=' . rawurlencode($category['slug'])) ?>"><?= icon($category['slug'] === 'cables' ? 'cables' : $category['slug']) ?><?= e($category['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="carousel" data-carousel tabindex="0" aria-label="Productos destacados">
        <?php foreach ($featured as $k => $product): ?>
          <div class="carousel-slide"><?= product_card($product, $k) ?></div>
        <?php endforeach; ?>
      </div>
      <div class="center reveal"><a class="btn btn-glass btn-lg" href="<?= url('tienda.php') ?>">Ver toda la tienda <?= icon('flecha') ?></a></div>
    </div>
  </section>

  <section id="nosotros" class="section" aria-labelledby="nosotros-titulo">
    <div class="wrap split">
      <div class="split-media reveal">
        <?= picture($images['nosotros'], '(min-width: 1000px) 560px, 100vw', 1000, 1100) ?>
        <div class="split-badge"><?= icon('escudo') ?><span>Trabajo ordenado, documentado y con soporte</span></div>
      </div>
      <div>
        <header class="section-head section-head-left reveal">
          <p class="kicker">Por qué RedTech</p>
          <h2 id="nosotros-titulo">Tecnología bien instalada, desde el primer día</h2>
          <p>Somos tu aliado técnico para conectar, proteger y hacer crecer tu operación.</p>
        </header>
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

  <section id="proceso" class="section section-tint" aria-labelledby="proceso-titulo">
    <div class="wrap">
      <header class="section-head reveal">
        <p class="kicker">Cómo trabajamos</p>
        <h2 id="proceso-titulo">Un proceso claro, de principio a fin</h2>
      </header>
      <ol class="steps" data-steps>
        <?php foreach ($content['steps'] as $i => $step): ?>
          <li class="step reveal" style="--i:<?= $i ?>">
            <span class="step-number" aria-hidden="true"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= e($step['title']) ?></h3>
            <p><?= e($step['text']) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="section" aria-labelledby="sectores-titulo">
    <div class="wrap">
      <header class="section-head reveal">
        <p class="kicker">Sectores</p>
        <h2 id="sectores-titulo">Soluciones para cada tipo de espacio</h2>
      </header>
      <div class="sectors">
        <?php foreach ($content['sectors'] as $i => $sector): ?>
          <article class="sector reveal" style="--i:<?= $i ?>">
            <?= picture($images[$sector['image']], '(min-width: 1000px) 300px, (min-width: 600px) 50vw, 100vw', 600, 760) ?>
            <h3><?= e($sector['name']) ?></h3>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="cta-band" aria-labelledby="cta-titulo">
    <div class="wrap cta-inner reveal">
      <div>
        <h2 id="cta-titulo">¿Listo para modernizar tu red?</h2>
        <p>Cuéntanos tu proyecto y te enviamos una propuesta sin compromiso.</p>
      </div>
      <div class="cta-actions">
        <a class="btn btn-light btn-lg" href="#contacto">Solicitar cotización</a>
        <?php if ($waQuote !== ''): ?><a class="btn btn-whatsapp btn-lg" href="<?= e($waQuote) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a><?php endif; ?>
      </div>
    </div>
  </section>

  <section id="preguntas" class="section" aria-labelledby="preguntas-titulo">
    <div class="wrap faq-wrap">
      <header class="section-head section-head-left reveal">
        <p class="kicker">Preguntas frecuentes</p>
        <h2 id="preguntas-titulo">Resolvemos tus dudas</h2>
        <p>¿No encuentras lo que buscas? Escríbenos y te ayudamos.</p>
      </header>
      <div class="faq reveal" style="--i:1">
        <?php foreach ($content['faq'] as $item): ?>
          <details>
            <summary><?= e($item['q']) ?><span class="faq-sign" aria-hidden="true"></span></summary>
            <p><?= e($item['a']) ?></p>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="contacto" class="section contact" aria-labelledby="contacto-titulo">
    <div class="wrap contact-grid">
      <div class="contact-info reveal">
        <p class="kicker kicker-light">Contacto</p>
        <h2 id="contacto-titulo">Hablemos de tu proyecto</h2>
        <p>Te respondemos para resolver tus dudas y preparar tu cotización.</p>
        <ul class="contact-list">
          <?php foreach ($site['contacts'] as $contact):
              $confirmed = (int) $contact['confirmed'] === 1;
              $href = $contact['type'] === 'phone' ? 'tel:' . preg_replace('/[^0-9+]/', '', $contact['value']) : 'mailto:' . $contact['value'];
          ?>
            <li>
              <span class="contact-icon"><?= icon($contact['type'] === 'phone' ? 'telefono' : 'correo') ?></span>
              <div><small><?= e($contact['label']) ?></small>
                <?php if ($confirmed): ?><a href="<?= e($href) ?>"><?= e($contact['value']) ?></a><?php else: ?><span><?= e($contact['value']) ?> <?= $pending ?></span><?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
          <?php $location = $site['settings']['location']; ?>
          <li>
            <span class="contact-icon"><?= icon('ubicacion') ?></span>
            <div><small>Ubicación</small><span><?= e($location['value']) ?><?= (int) $location['confirmed'] === 1 ? '' : ' ' . $pending ?></span>
              <?php if (setting('map_url') !== ''): ?><a class="map-link" href="<?= e(setting('map_url')) ?>" target="_blank" rel="noopener">Ver en el mapa</a><?php endif; ?>
            </div>
          </li>
        </ul>
      </div>

      <?php if ($sent): ?>
        <div class="form-card form-sent reveal">
          <span class="sent-icon"><?= icon('check') ?></span>
          <p class="notice" role="status" tabindex="-1" data-focus>Recibimos tu solicitud. Te contactaremos pronto.</p>
          <a class="link-arrow" href="./#contacto">Enviar otra solicitud <?= icon('flecha') ?></a>
        </div>
      <?php else: ?>
        <form class="form-card reveal" method="post" action="./#contacto" novalidate>
          <h3>Solicita tu cotización</h3>
          <?php if (isset($errors['form'])): ?>
            <p class="alert" role="alert" tabindex="-1" data-focus><?= e($errors['form']) ?></p>
          <?php elseif ($errors !== []): ?>
            <p class="alert" role="alert" tabindex="-1" data-focus>Revisa los campos marcados para enviar tu solicitud.</p>
          <?php endif; ?>
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <div class="hp" aria-hidden="true"><label>No llenar <input name="website" tabindex="-1" autocomplete="off"></label></div>
          <?php
          $field = static function (string $name, string $label, string $type, string $autocomplete) use ($old, $errors): void {
              $error = $errors[$name] ?? null; ?>
              <div class="field<?= $error ? ' has-error' : '' ?>">
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
              <option value="">Elegir un servicio</option>
              <?= $interestOptions($selected) ?>
            </select>
            <?php if (isset($errors['interest'])): ?><p class="error" id="e-interest"><?= e($errors['interest']) ?></p><?php endif; ?>
          </div>
          <div class="field field-wide<?= isset($errors['message']) ? ' has-error' : '' ?>">
            <label for="f-message">Mensaje (opcional)</label>
            <textarea id="f-message" name="message" rows="4" placeholder="Cuéntanos brevemente tu proyecto"><?= e($old['message'] ?? '') ?></textarea>
            <?php if (isset($errors['message'])): ?><p class="error" id="e-message"><?= e($errors['message']) ?></p><?php endif; ?>
          </div>
          <button class="btn btn-primary btn-lg btn-block" type="submit">Enviar solicitud <?= icon('flecha') ?></button>
        </form>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php page_footer(); ?>
