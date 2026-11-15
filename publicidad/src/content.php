<?php
// Textos del sitio de RedTech Publicidad. Son una propuesta inicial: revísalos y ajústalos.
// La página de Facebook describe el negocio como "Diseño Gráfico, Creación, Elaboración,
// Impresión y Rotulación"; los detalles de cada servicio son una propuesta por confirmar.
// Los datos de contacto, cifras, fotos y productos se editan desde /admin.

return [
    'hero' => [
        'eyebrow' => 'Publicidad & diseño',
        'title_before' => 'Haz que tu marca',
        'rotating' => ['destaque', 'se vea', 'venda', 'crezca'],
        'title_after' => 'con publicidad que se nota',
        'lead' => 'Diseño gráfico, impresión, rotulación y material publicitario para tu negocio. Nosotros lo diseñamos, lo producimos y lo instalamos.',
    ],
    'marquee' => ['Diseño gráfico', 'Logos', 'Gigantografías', 'Lonas', 'Vinilos', 'Adhesivos', 'Tarjetas', 'Volantes', 'Rotulación', 'Letreros', 'Vehículos rotulados', 'Material POP', 'Señalética'],
    'services' => [
        'diseno' => [
            'name' => 'Diseño gráfico',
            'detail' => 'Creamos la imagen de tu marca y las piezas para que tus clientes te reconozcan.',
            'items' => ['Logos e identidad de marca', 'Artes para redes sociales', 'Diseño de piezas impresas'],
        ],
        'impresion' => [
            'name' => 'Impresión',
            'detail' => 'Imprimimos en pequeño y gran formato con colores fieles a tu diseño.',
            'items' => ['Gigantografías y lonas', 'Vinilos y adhesivos', 'Tarjetas, volantes y papelería'],
        ],
        'rotulacion' => [
            'name' => 'Rotulación',
            'detail' => 'Llevamos tu marca a la calle: fachadas, vitrinas y vehículos.',
            'items' => ['Letreros para locales', 'Rotulación de vitrinas', 'Vehículos rotulados'],
        ],
    ],
    'extras' => [
        ['icon' => 'elaboracion', 'name' => 'Creación y elaboración', 'text' => 'Material publicitario, exhibidores y señalética hechos a la medida de tu espacio.'],
        ['icon' => 'chat', 'name' => 'Campañas y promociones', 'text' => 'Piezas listas para inauguraciones, temporadas y ofertas: impresas y para redes.'],
        ['icon' => 'soporte', 'name' => 'Instalación', 'text' => 'Instalamos letreros, lonas y vinilos para que todo quede bien puesto.'],
    ],
    'reasons' => [
        ['title' => 'Todo en un solo lugar', 'text' => 'Diseño, impresión, rotulación e instalación con el mismo equipo de principio a fin.'],
        ['title' => 'Ves la prueba antes', 'text' => 'Te enviamos el diseño para que lo apruebes antes de producir.'],
        ['title' => 'Acabados que duran', 'text' => 'Materiales pensados para interior y exterior, para que tu publicidad luzca por más tiempo.'],
        ['title' => 'Atención cercana', 'text' => 'Te asesoramos por WhatsApp y coordinamos contigo la entrega o la instalación.'],
    ],
    'steps' => [
        ['title' => 'Nos cuentas tu idea', 'text' => 'Escríbenos qué necesitas, medidas y fecha. Si hace falta, visitamos el lugar.'],
        ['title' => 'Diseño y prueba', 'text' => 'Preparamos el diseño y te enviamos una prueba para que la apruebes.'],
        ['title' => 'Producción', 'text' => 'Imprimimos o fabricamos tu pieza con los materiales acordados.'],
        ['title' => 'Entrega e instalación', 'text' => 'Te la entregamos o la instalamos en tu local o vehículo.'],
    ],
    'sectors' => [
        ['image' => 'sector_comercios', 'name' => 'Comercios y locales'],
        ['image' => 'sector_eventos', 'name' => 'Eventos e inauguraciones'],
        ['image' => 'sector_emprendedores', 'name' => 'Emprendedores'],
        ['image' => 'sector_empresas', 'name' => 'Empresas e instituciones'],
    ],
    'faq' => [
        ['q' => '¿Cómo pido una cotización?', 'a' => 'Completa el formulario o escríbenos por WhatsApp con lo que necesitas (tipo de trabajo, medidas y cantidad). Te respondemos con una propuesta.'],
        ['q' => '¿Me ayudan con el diseño?', 'a' => 'Sí. Si no tienes diseño, lo creamos y te enviamos una prueba antes de imprimir. Si ya lo tienes, lo revisamos para que salga bien impreso.'],
        ['q' => '¿Cómo compro en la tienda?', 'a' => 'Agrega los productos al carrito y pulsa "Enviar pedido por WhatsApp". Se abrirá un mensaje con tu pedido y coordinamos contigo diseño, pago y entrega.'],
        ['q' => '¿Instalan letreros y rótulos?', 'a' => 'Sí. Coordinamos contigo la visita y la instalación del letrero, la lona o los vinilos.'],
        ['q' => '¿Cuánto se demora un trabajo?', 'a' => 'Depende del tipo de trabajo y la cantidad. Te damos la fecha de entrega junto con la cotización.'],
    ],
];
