<?php
// Textos del sitio. Son una propuesta inicial: revísalos y ajústalos a tu empresa.
// Los datos de contacto, cifras, fotos y productos se editan desde /admin.

return [
    'hero' => [
        'eyebrow' => 'Publicidad & Tecnología',
        'title_before' => 'Soluciones en',
        'rotating' => ['redes', 'seguridad', 'publicidad', 'diseño'],
        'title_after' => 'para tu negocio',
        'lead' => 'Instalamos cableado estructurado, cámaras de seguridad y servidores, y hacemos que tu marca destaque con diseño gráfico, impresión y rotulación. Todo con un mismo equipo.',
    ],
    'marquee' => ['Cableado Cat6 y Cat6A', 'Fibra óptica', 'Cámaras IP', 'Grabadores NVR', 'Servidores', 'Almacenamiento NAS', 'Routers', 'Switches PoE', 'Wi-Fi profesional', 'Diseño gráfico', 'Gigantografías', 'Rotulación', 'Letreros', 'Soporte técnico'],
    'services' => [
        'cableado' => [
            'name' => 'Cableado estructurado',
            'detail' => 'Redes de datos ordenadas, etiquetadas y documentadas, listas para crecer con tu operación.',
            'items' => ['Cableado Cat6, Cat6A y fibra óptica', 'Racks, patch panels y organización', 'Certificación y etiquetado de puntos'],
        ],
        'camaras' => [
            'name' => 'Cámaras de seguridad',
            'detail' => 'Videovigilancia IP con grabación continua y acceso desde tu celular, donde estés.',
            'items' => ['Cámaras IP interiores y exteriores', 'Grabadores NVR y almacenamiento', 'Monitoreo remoto desde la app'],
        ],
        'servidores' => [
            'name' => 'Servidores',
            'detail' => 'Servidores y almacenamiento para tus aplicaciones, archivos y copias de seguridad.',
            'items' => ['Instalación y configuración', 'Almacenamiento NAS y respaldos', 'Virtualización y mantenimiento'],
        ],
    ],
    // Línea de publicidad (RedTech Publicidad & Tecnología). Los detalles de cada servicio son
    // una propuesta: la página de Facebook solo dice "Diseño Gráfico, Creación, Elaboración,
    // Impresión y Rotulación". Confírmalos o ajústalos.
    'ads' => [
        'kicker' => 'Publicidad & diseño',
        'title' => '¡Haz que tu marca destaque!',
        'lead' => 'Diseñamos, imprimimos y rotulamos todo lo que tu negocio necesita para que lo vean, lo recuerden y lo elijan.',
        'services' => [
            ['icon' => 'diseno', 'name' => 'Diseño gráfico', 'text' => 'Logos, identidad de marca, artes para redes sociales y piezas para tus campañas.'],
            ['icon' => 'imprimir', 'name' => 'Impresión', 'text' => 'Gigantografías, lonas, vinilos, adhesivos, tarjetas, volantes y más.'],
            ['icon' => 'rotulo', 'name' => 'Rotulación', 'text' => 'Letreros, fachadas, vitrinas y vehículos rotulados con tu marca.'],
            ['icon' => 'elaboracion', 'name' => 'Creación y elaboración', 'text' => 'Material publicitario, señalética y exhibidores hechos a la medida.'],
        ],
        'combo' => [
            'title' => 'Tecnología y publicidad en un solo lugar',
            'text' => '¿Abres un local u oficina? Te instalamos la red, las cámaras y el Wi-Fi, y también el letrero, la rotulación y el material para tu inauguración.',
        ],
    ],
    'extras' => [
        ['icon' => 'wifi', 'name' => 'Redes y Wi-Fi', 'text' => 'Cobertura inalámbrica estable en toda tu instalación, con puntos de acceso profesionales.'],
        ['icon' => 'soporte', 'name' => 'Soporte y mantenimiento', 'text' => 'Revisiones preventivas y atención cuando algo falla, para que tu red no se detenga.'],
        ['icon' => 'tienda', 'name' => 'Venta de equipos', 'text' => 'Cables, servidores, routers y cámaras con asesoría para elegir lo que de verdad necesitas.'],
    ],
    'reasons' => [
        ['title' => 'Un solo proveedor', 'text' => 'Red, seguridad, servidores, equipos y publicidad con el mismo equipo de principio a fin.'],
        ['title' => 'Instalaciones ordenadas', 'text' => 'Trabajo limpio, etiquetado y documentado para que tu red sea fácil de mantener.'],
        ['title' => 'Asesoría honesta', 'text' => 'Te recomendamos lo que tu proyecto necesita, sin equipos que no vas a usar.'],
        ['title' => 'Acompañamiento', 'text' => 'Seguimos contigo después de la instalación con soporte y mantenimiento.'],
    ],
    'steps' => [
        ['title' => 'Diagnóstico', 'text' => 'Nos cuentas tu necesidad y, si hace falta, visitamos el lugar.'],
        ['title' => 'Propuesta', 'text' => 'Te enviamos una cotización clara con equipos y tiempos.'],
        ['title' => 'Instalación', 'text' => 'Ejecutamos el proyecto de forma ordenada y documentada.'],
        ['title' => 'Soporte', 'text' => 'Te acompañamos con mantenimiento y atención técnica.'],
    ],
    'sectors' => [
        ['image' => 'empresas', 'name' => 'Oficinas y corporativos'],
        ['image' => 'servidores', 'name' => 'Industria y bodegas'],
        ['image' => 'nosotros', 'name' => 'Comercios y locales'],
        ['image' => 'hogares', 'name' => 'Hogares y residencias'],
    ],
    'faq' => [
        ['q' => '¿Cómo pido una cotización?', 'a' => 'Completa el formulario de contacto o escríbenos por WhatsApp contándonos qué necesitas. Te responderemos con una propuesta.'],
        ['q' => '¿Cómo compro en la tienda?', 'a' => 'Agrega los productos al carrito y pulsa "Enviar pedido por WhatsApp". Se abrirá un mensaje con tu pedido listo para enviar y coordinamos contigo el pago y la entrega.'],
        ['q' => '¿Puedo comprar equipos sin contratar la instalación?', 'a' => 'Sí. Puedes comprar solo los equipos o pedirnos también la instalación y configuración.'],
        ['q' => '¿También hacen publicidad?', 'a' => 'Sí. Hacemos diseño gráfico, impresión, rotulación y material publicitario. Escríbenos por WhatsApp con tu idea y te cotizamos.'],
        ['q' => '¿Atienden a empresas y a hogares?', 'a' => 'Sí. Trabajamos en oficinas, industrias, comercios y viviendas.'],
        ['q' => '¿Ofrecen mantenimiento después de la instalación?', 'a' => 'Sí. Ofrecemos soporte técnico y mantenimiento preventivo para tu red, cámaras y servidores.'],
    ],
];
