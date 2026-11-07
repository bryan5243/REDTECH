---
name: RedTech
description: Tablero de señalización de planta para cableado, cámaras, servidores y tienda.
colors:
  amarillo: "#ffc400"
  amarillo-vivo: "#ffd53d"
  rojo: "#b3121d"
  azul: "#0a4fa3"
  verde: "#00704a"
  tinta: "#14130e"
  tinta-alzada: "#33322b"
  linea-tienda: "#6c6b63"
  texto-suave: "#4b4c47"
  acero: "#d8dad7"
  acero-claro: "#eceee9"
  esmalte: "#ffffff"
typography:
  display:
    fontFamily: "Barlow Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(2.75rem, min(6.2vw, 9svh), 6rem)"
    fontWeight: 700
    lineHeight: 0.98
    letterSpacing: "-0.01em"
  headline:
    fontFamily: "Barlow Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(2.75rem, 6vw, 5.5rem)"
    fontWeight: 700
    lineHeight: 0.98
    letterSpacing: "-0.01em"
  title:
    fontFamily: "Barlow Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(2rem, min(4.2vw, 6svh), 4.25rem)"
    fontWeight: 700
    lineHeight: 1.05
  subtitle:
    fontFamily: "Barlow Condensed, Arial Narrow, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.1
  button:
    fontFamily: "Barlow Condensed, Arial Narrow, sans-serif"
    fontSize: "1.375rem"
    fontWeight: 700
    lineHeight: 1
  lead:
    fontFamily: "Barlow, Segoe UI, sans-serif"
    fontSize: "clamp(1.125rem, 1.6vw, 1.375rem)"
    fontWeight: 400
    lineHeight: 1.45
  body:
    fontFamily: "Barlow, Segoe UI, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.55
  label:
    fontFamily: "Barlow, Segoe UI, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 600
  mark:
    fontFamily: "Barlow, Segoe UI, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 600
    lineHeight: 1.5
rounded:
  placa: "10px"
  selector: "8px"
  filete: "7px"
  control: "6px"
  opcion: "5px"
  marca: "4px"
spacing:
  ranura: "6px"
  campo: "16px"
  gutter: "clamp(16px, 2.6vw, 40px)"
  banda: "clamp(36px, 6vw, 88px)"
  columna: "clamp(28px, 5vw, 80px)"
components:
  button-primary:
    backgroundColor: "{colors.amarillo}"
    textColor: "{colors.tinta}"
    typography: "{typography.button}"
    rounded: "{rounded.control}"
    padding: "10px 24px"
    height: "46px"
  button-primary-hover:
    backgroundColor: "{colors.amarillo-vivo}"
  input:
    backgroundColor: "{colors.esmalte}"
    textColor: "{colors.tinta}"
    typography: "{typography.body}"
    rounded: "{rounded.control}"
    padding: "10px 12px"
    height: "46px"
  input-hover:
    backgroundColor: "{colors.acero-claro}"
  topbar:
    backgroundColor: "{colors.tinta}"
    textColor: "{colors.esmalte}"
    padding: "0 clamp(16px, 2.6vw, 40px)"
    height: "56px"
  audience-selector:
    backgroundColor: "{colors.tinta}"
    textColor: "{colors.esmalte}"
    rounded: "{rounded.selector}"
    padding: "4px"
  audience-option-hover:
    backgroundColor: "{colors.tinta-alzada}"
    rounded: "{rounded.opcion}"
  audience-option-current:
    backgroundColor: "{colors.amarillo}"
    textColor: "{colors.tinta}"
    rounded: "{rounded.opcion}"
  row-cableado:
    backgroundColor: "{colors.azul}"
    textColor: "{colors.esmalte}"
    typography: "{typography.title}"
    rounded: "{rounded.placa}"
  row-camaras:
    backgroundColor: "{colors.amarillo}"
    textColor: "{colors.tinta}"
    typography: "{typography.title}"
    rounded: "{rounded.placa}"
  row-servidores:
    backgroundColor: "{colors.verde}"
    textColor: "{colors.esmalte}"
    typography: "{typography.title}"
    rounded: "{rounded.placa}"
  row-tienda:
    backgroundColor: "{colors.tinta}"
    textColor: "{colors.amarillo}"
    typography: "{typography.title}"
    rounded: "{rounded.placa}"
  plate-quote:
    backgroundColor: "{colors.rojo}"
    textColor: "{colors.esmalte}"
    rounded: "{rounded.placa}"
    padding: "clamp(36px, 6vw, 88px) clamp(16px, 2.6vw, 40px)"
  pending-mark:
    typography: "{typography.mark}"
    rounded: "{rounded.marca}"
    padding: "0 7px"
---

# Design System: RedTech

## Overview

**Creative North Star: "El tablero de señalización de planta"**

RedTech se presenta como el tablero que se encuentra a la entrada de una planta: se lee de un vistazo y dirige a cada visitante hacia lo que necesita. Placas esmaltadas a todo el ancho, separadas por una ranura estrecha, descansan sobre acero gris claro. El color no adorna: nombra cosas. Quien aprende que el azul es cableado en la primera pantalla lo reconoce de nuevo al bajar.

La densidad es alta y la voz, grande. Una placa por idea, con el nombre en condensada pesada y la explicación en una línea. Nada flota ni se apila en sombras: la profundidad es la de una chapa atornillada a otra chapa. El movimiento es igual de literal, porque los rótulos giran sobre su eje cuando cambia el público elegido.

El sistema rechaza el azul oscuro corporativo y las filas de tres tarjetas iguales. Todavía no define un tratamiento fotográfico: los pictogramas propios llevan toda la carga visual, y el logo está pendiente, así que la marca es hoy un rótulo de texto.

**Key Characteristics:**
- Placas de color pleno a todo el ancho sobre acero, separadas por una ranura de 6px.
- Código de color funcional: cada servicio conserva su color en toda la página.
- Barlow Condensed en rótulos y Barlow en texto, siempre en mayúscula inicial.
- Plano por completo: filete interior y ranura en lugar de sombras.
- Pictogramas propios de trazo uniforme, con una flecha que cierra cada fila.

## Colors

Cinco colores de señalización saturados sobre neutros de acero y una tinta ligeramente cálida.

### Primary
- **Amarillo Señal** (#ffc400): tiene dos trabajos. Es la placa de Cámaras y es el color de acción y de lo activo: botones, botón Cotizar, opción elegida del selector, títulos de la tienda sobre tinta y avisos. Lleva siempre texto en tinta (11.6:1).
- **Amarillo Vivo** (#ffd53d): solo para el hover de los botones amarillos.

### Secondary
- **Rojo Cotización** (#b3121d): la placa de pedir cotización y la de contacto, con texto en esmalte (6.9:1). Fuera de esas placas solo aparece en la palabra variable del titular principal y en el cursor de los campos.

### Tertiary
- **Azul Cableado** (#0a4fa3): fila y banda de Cableado estructurado, con texto en esmalte (7.9:1).
- **Verde Servidores** (#00704a): fila y banda de Servidores, con texto en esmalte (6.1:1).

### Neutral
- **Tinta de Planta** (#14130e): placa de la tienda, barra superior, pie de página, selector, bordes de campo y todo el texto sobre claro.
- **Tinta Alzada** (#33322b): hover de las opciones del selector sobre tinta.
- **Línea de Tienda** (#6c6b63): divisores de la lista de productos sobre tinta.
- **Texto Suave** (#4b4c47): párrafo de apoyo de la placa blanca (8.7:1 sobre esmalte).
- **Acero Galvanizado** (#d8dad7): fondo de la página, visible en la ranura entre placas.
- **Acero Claro** (#eceee9): hover de los campos de formulario.
- **Esmalte Blanco** (#ffffff): placas neutras de presentación y de empresa, fondo de campos y texto sobre placas oscuras.

### Named Rules
**The Código de Color Rule.** Azul es cableado, amarillo es cámaras, verde es servidores, tinta es tienda y rojo es cotización. Un servicio nuevo no toma prestado el color de otro, y el color se aplica a la placa entera. La única excepción sobre blanco es la palabra variable del titular principal, en rojo.

**The Doble Amarillo Rule.** El amarillo es a la vez placa de Cámaras y señal de acción. La acción amarilla vive sobre tinta y sobre rojo; sobre una placa amarilla, la acción pasa a ser un enlace subrayado en tinta.

**The Estado Como Marca Rule.** Un estado persistente se ve como una marca, no como un matiz: borde de 4px en el error, filete discontinuo en lo que falta confirmar, línea de 4px y texto mayor en el caso elegido. Solo el hover puede resolverse con un cambio de tono.

## Typography

**Display Font:** Barlow Condensed (con Arial Narrow y sans-serif)
**Body Font:** Barlow (con Segoe UI y sans-serif)

**Character:** Una grotesca de señalética vial en dos anchos. La condensada grita el nombre desde lejos y la normal lo explica de cerca. Hoy ambas se cargan desde Google Fonts y no están autoalojadas.

### Hierarchy
- **Display** (700, clamp de 2.75rem a 6rem limitado por vw y svh, 0.98): el titular principal, hasta 26ch.
- **Headline** (700, clamp de 2.75rem a 5.5rem, 0.98): títulos de banda y de sección. La tienda sube hasta 6rem y sus productos llegan a 5rem con interlínea 1.
- **Title** (700, clamp de 2rem a 4.25rem limitado por vw y svh, 1.05): nombre de cada fila del tablero.
- **Subtitle** (700, 1.5rem, 1.1): términos como "En empresas"; el caso elegido sube a 2.25rem.
- **Button** (700, 1.375rem, 1): botones. El botón Cotizar usa 1.25rem y las opciones del selector, 1.5rem.
- **Lead** (400, clamp de 1.125rem a 1.375rem, 1.45): párrafo principal de banda, hasta 46ch.
- **Body** (400, 1.0625rem y 1rem bajo 760px, 1.55): texto corrido y campos, estos en peso 500.
- **Label** (600, 0.9375rem): etiquetas del formulario rápido y mensajes de error.
- **Mark** (600, 0.8125rem, 1.5): la marca "por confirmar", el texto más pequeño del sistema.

Los títulos usan text-wrap balance y un tracking de -0.01em. Las cifras de empresa y los teléfonos usan numerales tabulares.

### Named Rules
**The Rótulo Rule.** Todo lo que se lee de lejos va en Barlow Condensed 700: títulos, botones, nombres de fila y términos. El texto corrido nunca va en condensada.

**The Caja Baja Rule.** Los rótulos van en mayúscula inicial y minúsculas. El sistema no usa mayúsculas sostenidas ni tracking abierto.

**The Tablero Cabe Rule.** Los títulos del tablero se limitan también por la altura de la ventana (svh), para que las placas de la primera pantalla quepan sin recortarse.

## Layout

La página es una pila de bandas horizontales a todo el ancho. La ranura de acero de 6px separa cada placa de sus vecinas y de los bordes de la ventana. El gutter interior va de 16px a 40px.

El tablero de la primera pantalla ocupa la ventana menos la barra superior (entre 640px y 1100px de alto): placa de presentación, cuatro filas de directorio de alto igual y la fila de cotización. Dentro de las bandas de servicio hay una retícula de 12 columnas con pictograma (2), contenido (6) y casos (4); el enlace se alinea con el contenido. La banda amarilla invierte el orden y pone el pictograma a la derecha. Tienda, empresa y contacto usan dos columnas en proporción 5:7 o 7:5.

Hay dos puntos de quiebre. A 1080px, el tablero pierde su alto fijo, las filas pasan la descripción bajo el nombre y las bandas se apilan a una columna. A 760px, la navegación baja a su propia línea, el selector ocupa todo el ancho y los formularios y bloques de dos columnas pasan a una. El relleno vertical de las bandas va de 36px a 88px.

Al bajar, cada banda de color domina la pantalla. En el tablero, en cambio, las placas comparten la ventana y ninguna fila de directorio llega a ocupar un tercio de ella.

**The Banda Completa Rule.** El contenido vive en bandas horizontales completas. Una sección nueva es una placa más en la pila, no una rejilla de tarjetas dentro de ella.

## Elevation & Depth

El sistema es plano: no hay ninguna sombra. La profundidad viene de la ranura de acero entre placas y del filete interior, que hace leer cada placa como una pieza física. La barra superior fija tampoco lleva sombra; se separa por el contraste de la tinta.

**The Chapa Plana Rule.** Las superficies no flotan. Ni el hover ni el foco añaden elevación: responden con color, subrayado o desplazamiento de la flecha.

## Shapes

Esquinas moderadas de chapa esmaltada, con radios que disminuyen hacia dentro: placa (10px) y su filete (7px), selector (8px) y su opción (5px). Los controles sueltos usan 6px y la marca, 4px. La barra superior y el pie son franjas de tinta sin radio.

Los divisores son líneas de 1.5px en color de tinta, de texto o de línea de tienda. Los trazos gruesos se reservan: 2px para bordes de campo y subrayados de enlace, 4px para estados.

**The Filete Rule.** Toda placa lleva una línea interior de 1.5px a 4px del borde, en el color del texto al 50%. Es la firma del sistema, y sin ella una placa es solo un rectángulo de color.

## Components

### Buttons
Macizos y directos, como un pulsador de obra.
- **Shape:** esquinas de control (6px), al menos 46px de alto.
- **Primary:** fondo amarillo y texto en tinta, en condensada 700, con relleno de 10px y 24px. Ocupa todo el ancho bajo 760px.
- **Hover / Focus:** pasa a Amarillo Vivo en 180ms y al pulsar baja 1px. El foco es un contorno de 3px con separación de 3px, en tinta sobre placas claras y amarillo sobre oscuras.
- **Enlace de banda:** condensada 700 subrayada a 2px con flecha, que avanza 6px en hover. Es la acción en las placas de servicio y en la tienda.

### Selector Empresa / Hogar
La interacción firma del sistema. Un carril de tinta con 4px de relleno y dos opciones de condensada 700 a 1.5rem. La opción activa es amarilla con texto en tinta.
- **Giro de rótulos:** al cambiar, cada texto variable sale en 150ms rotando 70 grados sobre su eje horizontal, cambia y entra en 340ms, con 45ms de cascada entre rótulos.
- **Sin movimiento:** con prefers-reduced-motion el texto cambia al instante y se anulan todas las animaciones y transiciones.

### Placas de directorio
Filas del tablero, cada una un enlace completo en el color de su servicio: pictograma, nombre en Title, descripción en Barlow 500 y flecha. En hover, la flecha avanza 10px en 220ms. El foco se dibuja dentro de la placa, 8px hacia adentro.

### Bandas de servicio
- **Corner Style:** esquinas de placa (10px), con filete.
- **Background:** el color del servicio, de borde a borde.
- **Internal Padding:** de 36px a 88px en vertical y gutter en horizontal.
- **Casos por público:** "En empresas" y "En hogares" se muestran siempre. El caso del público elegido sube primero, gana una línea superior de 4px, un término de 2.25rem y texto de 1.25rem en peso 500.

### Lista de la tienda
Sobre tinta, filas separadas por líneas de 1.5px con pictograma amarillo, nombre en condensada de 2.5rem a 5rem y flecha amarilla. En hover, el nombre se vuelve amarillo y la flecha avanza 8px.

### Inputs / Fields
- **Style:** fondo esmalte, borde de 2px en tinta, esquinas de control (6px), 46px de alto mínimo y cursor rojo. El select lleva un chevrón de trazo 3.
- **Hover / Focus:** el fondo pasa a Acero Claro; el contorno de foco se separa 1px.
- **Error:** el borde pasa a amarillo de 4px y el mensaje aparece debajo en Label. Esta gramática solo está resuelta sobre la placa roja, donde se muestra hoy.
- **Avisos:** placa de 6px de radio en peso 600. Amarillo para revisar y esmalte para confirmar el envío; ambos con texto en tinta.

### Navigation
Barra fija de tinta de 56px de alto. Rótulo RedTech en condensada 700 a 1.75rem, enlaces en Barlow 500 a 1rem y botón Cotizar amarillo con esquinas de 6px. En hover, los enlaces pasan a amarillo con subrayado de 2px. Bajo 760px, la barra se parte en dos líneas y los enlaces se reparten a lo ancho.

### Marca "por confirmar"
Etiqueta en línea con filete discontinuo de 1.5px en el color del texto y esquinas de 4px. Acompaña a todo teléfono, correo, ubicación o cifra sin validar por el propietario, en cualquier placa.

### Pictogramas
Dibujos propios en SVG de 48 por 48, sin relleno, con trazo 3 de extremos redondeados y en el color del texto. Hay uno por servicio, uno para la tienda y otro para routers, más una flecha compartida. Los demás productos reutilizan el de su servicio. Cambian de tamaño con la tipografía que los rodea, hasta 8.5rem en las bandas.

## Do's and Don'ts

### Do:
- **Do** dar a cada placa fondo pleno, esquinas de 10px, filete interior y 6px de ranura de acero alrededor.
- **Do** mantener el código de color en toda página: azul cableado, amarillo cámaras, verde servidores, tinta tienda y rojo cotización.
- **Do** escribir texto en tinta sobre amarillo y en esmalte sobre azul, verde, rojo y tinta.
- **Do** cerrar cada destino con la flecha compartida y moverla en hover con la curva de salida (cubic-bezier(0.16, 1, 0.3, 1)).
- **Do** marcar con "por confirmar" todo dato de la empresa sin validar.
- **Do** cambiar el foco entre contorno de tinta sobre placas claras y amarillo sobre oscuras.
- **Do** respetar prefers-reduced-motion en cualquier animación nueva.

### Don't:
- **Don't** usar sombras: el sistema se sostiene con ranura y filete.
- **Don't** usar azul oscuro corporativo como fondo ni filas de tres tarjetas iguales.
- **Don't** asignar a un color del código un tema que no le corresponde, ni sumar acentos de color sobre placas blancas más allá de la palabra variable del titular.
- **Don't** poner un botón amarillo sobre la placa amarilla; usa el enlace subrayado en tinta.
- **Don't** escribir rótulos en mayúsculas sostenidas ni texto corrido en condensada.
- **Don't** señalar un error o una selección solo con un cambio de tono.
- **Don't** mezclar pictogramas de otras familias, con relleno o con otro grosor de trazo.
