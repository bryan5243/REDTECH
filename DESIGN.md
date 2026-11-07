---
name: RedTech
description: Integrador tecnológico corporativo, oscuro y dinámico, con tienda que vende por WhatsApp.
colors:
  navy-950: "#050f22"
  navy-900: "#081a35"
  ink: "#0b1a30"
  text: "#33445c"
  muted: "#64748b"
  line: "#e3e9f2"
  tint: "#f3f6fb"
  blue: "#1764ff"
  cyan: "#19c6e8"
  green: "#12b76a"
  amber: "#f59e0b"
  red: "#e5484d"
  whatsapp: "#1fae5b"
gradient: "linear-gradient(120deg, #1764ff 0%, #19c6e8 100%)"
typography:
  display: { fontFamily: "Sora", fontWeight: 700, fontSize: "clamp(2.2rem, 6vw, 4.2rem)", lineHeight: 1.15, letterSpacing: "-0.02em" }
  heading: { fontFamily: "Sora", fontWeight: 700, fontSize: "clamp(1.7rem, 3.6vw, 2.6rem)" }
  body: { fontFamily: "Inter", fontWeight: 400, fontSize: "1rem", lineHeight: 1.65 }
radius: { card: "18px", control: "12px", pill: "999px" }
---

# RedTech: sistema visual

Versión del 7 de noviembre de 2026. Reemplaza la dirección anterior de «tablero de señalización» por pedido del propietario: más profesional, empresarial, dinámica y con fotos de productos. Se quitó el selector Empresa/Hogar.

## Principios

1. **Corporativo y tecnológico.** Base azul marino profunda con un único acento en degradado azul a cian. Nada de colores de señalización.
2. **Movimiento con propósito.** Las animaciones guían la lectura (aparición escalonada, contadores, palabra rotativa, red animada) y se apagan con `prefers-reduced-motion`.
3. **Producto visible.** Fotos grandes en servicios, sectores y tarjetas de producto, con zoom sutil al pasar el mouse.
4. **Comprar es fácil.** Botón «Agregar» en cada tarjeta, carrito lateral y envío del pedido por WhatsApp en un clic.
5. **Datos honestos.** Lo no confirmado lleva la etiqueta «por confirmar»; el catálogo de ejemplo muestra su aviso.

## Superficies

- **Hero oscuro**: foto de fondo con zoom lento, cuadrícula, lienzo de red que reacciona al puntero, título con palabra rotativa, tarjeta de foto inclinable con tarjetas flotantes de vidrio.
- **Marquesina** de palabras clave técnicas.
- **Cifras** en una franja blanca superpuesta al hero, con contadores.
- **Tarjetas** blancas con borde suave que se elevan al pasar el mouse; ícono en degradado.
- **Secciones oscuras** para la tienda destacada y el contacto; claras (`tint`) para proceso y relacionados.
- **CTA** en banda de degradado con anillo girando.
- **Panel** (`/admin`): claro, sobrio, tablas que pasan a tarjetas en celular.

## Movimiento

| Efecto | Duración | Curva |
|---|---|---|
| Aparición al hacer scroll | 800 ms, retraso 90 ms por elemento | `cubic-bezier(.2,.7,.2,1)` |
| Hover de tarjeta | 400–450 ms, sube 6–8 px | igual |
| Zoom de imagen | 800 ms a 1 s, escala 1.07–1.1 | igual |
| Palabra rotativa | cada 2,8 s | igual |
| Carrusel | avanza cada 4,5 s, se pausa al pasar el mouse o con foco | — |

## Responsive

Diseño desde 360 px. Menú lateral en menos de 960 px, filtros de la tienda como fila desplazable, carrusel con desplazamiento táctil. Probado en 360, 390, 820 y 1440 px sin desplazamiento horizontal.
