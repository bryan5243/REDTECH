# RedTech: comparar versiones del sitio

Esta rama junta tres versiones completas del sitio para verlas y decidir cuál es mejor:

| Carpeta | Versión | Viene de la rama |
|---|---|---|
| `tecnologia/` | Solo tecnología (sitio actual) | `main` |
| `combinado/` | Publicidad + tecnología | `claude/project-thread-jwf6vv` |
| `marca/` | Solo publicidad | `claude/redtech-publicidad` |

`index.html` permite abrir cada una o compararlas lado a lado, en tamaño PC o celular.

## Verla en tu computadora

```bash
php -S localhost:8000
```

Abre http://localhost:8000. Cada versión funciona con su catálogo y datos de ejemplo, sin MySQL (el panel de administración no funciona en este modo).

## Verla en un hosting

Sube todo el contenido de esta rama a `htdocs/` o `public_html/`. La raíz muestra la página para elegir y cada carpeta sirve su sitio.

Esta rama es solo para comparar. Cuando elijas una versión, se fusiona su rama a `main`.

Nota: la carpeta de la versión de solo publicidad se llama `marca/` y las rutas internas evitan la palabra «publicidad», porque los bloqueadores de anuncios del navegador bloquean las direcciones que la contienen.
