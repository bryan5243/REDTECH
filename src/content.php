<?php

// Textos de la página. Teléfonos, correos, cifras e imágenes no van aquí: son datos editables.
// Con 'texts_confirmed' => false, el sitio marca estos textos como "por confirmar".
// Con 'show_placeholders' => false, desaparecen los espacios de certificaciones y testimonios.

return [
    'texts_confirmed' => false,
    'show_placeholders' => true,
    'lead' => [
        'empresa' => 'Cableado estructurado, cámaras de seguridad y servidores para la operación de tu empresa.',
        'hogar' => 'Cableado estructurado, cámaras de seguridad y servidores para la red y la seguridad de tu casa.',
    ],
    'services' => [
        'cableado' => [
            'name' => 'Cableado estructurado',
            'detail' => 'Instalamos la red de datos que conecta tus equipos, con cable en los puntos donde lo necesitas.',
            'empresa_detail' => 'Oficinas, bodegas y plantas industriales.',
            'hogar_detail' => 'Conexión por cable en las habitaciones donde más la usas.',
        ],
        'camaras' => [
            'name' => 'Cámaras de seguridad',
            'detail' => 'Instalamos y configuramos cámaras de seguridad en las zonas que quieres vigilar.',
            'empresa_detail' => 'Accesos, áreas de trabajo y zonas de almacenamiento.',
            'hogar_detail' => 'Entradas, patios y las zonas de la casa que quieras proteger.',
        ],
        'servidores' => [
            'name' => 'Servidores',
            'detail' => 'Instalamos y configuramos servidores según lo que necesitas guardar y compartir.',
            'empresa_detail' => 'Aplicaciones y archivos compartidos por tu equipo.',
            'hogar_detail' => 'Fotos, documentos y copias de tus archivos en un solo lugar.',
        ],
    ],
    'reasons' => [
        ['title' => 'Servicios y equipos juntos', 'text' => 'Red, seguridad y servidores, además de los equipos para cada uno.'],
        ['title' => 'Para empresas y hogares', 'text' => 'Atendemos tanto instalaciones empresariales como domésticas.'],
        ['title' => 'Cotización según tu caso', 'text' => 'Nos cuentas qué necesitas y preparamos una propuesta para ti.'],
    ],
    'steps' => [
        ['title' => 'Nos cuentas qué necesitas', 'text' => 'Completa el formulario con tu necesidad y tus datos de contacto.'],
        ['title' => 'Preparamos tu cotización', 'text' => 'Revisamos tu caso y te respondemos con una propuesta.'],
        ['title' => 'Coordinamos la instalación', 'text' => 'Si la aceptas, acordamos contigo los siguientes pasos.'],
    ],
    'sectors' => [
        'empresas' => ['name' => 'Empresas', 'items' => ['Oficinas', 'Bodegas', 'Plantas industriales']],
        'hogares' => ['name' => 'Hogares', 'items' => ['Red de datos en casa', 'Cámaras de seguridad', 'Almacenamiento de archivos']],
    ],
    'faq' => [
        ['q' => '¿Cómo pido una cotización?', 'a' => 'Completa el formulario con tu nombre, un teléfono o correo y lo que necesitas. Te contactaremos para continuar.'],
        ['q' => '¿Atienden a empresas y a hogares?', 'a' => 'Sí. Puedes elegir "Empresa" u "Hogar" en esta página para ver el caso que te corresponde.'],
        ['q' => '¿Puedo comprar equipos sin contratar una instalación?', 'a' => 'Estamos preparando el catálogo en línea. Mientras tanto, dinos qué equipo buscas y te responderemos.'],
        ['q' => '¿Qué equipos ofrecen?', 'a' => 'Cables, servidores, routers y cámaras. Consúltanos por el equipo que necesitas.'],
    ],
    'products' => [
        'cables' => ['name' => 'Cables', 'text' => 'Para tu red de datos.'],
        'servidores' => ['name' => 'Servidores', 'text' => 'Para guardar y compartir información.'],
        'routers' => ['name' => 'Routers', 'text' => 'Para conectar tu red a internet.'],
        'camaras' => ['name' => 'Cámaras', 'text' => 'Para vigilar las zonas que necesitas.'],
    ],
];
