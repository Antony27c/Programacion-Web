# BOXLY — Guía de estilo del rediseño

Rediseño visual del sitio (rama `feature/diseno`). El objetivo fue que BOXLY se vea **moderno, cálido y
confiable** para alguien que lleva su auto al taller: más aire, mejor jerarquía, llamadas a la acción
claras ("Sacar turno", "Pedir presupuesto"), formularios amigables y buena experiencia en el celular,
sin sumar frameworks: sigue siendo **Bootstrap 5.3 por CDN + `css/styles.css` propio**.

La paleta parte de la de los mockups Figma del TP3 (Ink `#0F172A` y azul `#0052FF`) y la amplié con
tonos claros del azul, un neutro arena para las secciones alternadas y un ámbar como toque cálido.

## Archivos

| Archivo | Qué tiene |
|---------|-----------|
| `css/styles.css` | Tokens (custom properties), base, componentes y responsive. Reescrito completo. |
| `js/main.js` | Micro-interacciones sin dependencias (foco en alertas, skip-link, sombra de navbar, aparición al scroll, contador de caracteres). |
| `includes/header.php` | Barra superior con horario/teléfono, navbar clara con estado activo + `aria-current`, CTA "Sacar turno" visible también en móvil, favicon. |
| `includes/footer.php` | Banda de cierre con CTA (en páginas informativas) y footer de 4 columnas con horarios unificados. |
| `img/hero-taller.svg` | Ilustración propia del hero (auto + tarjetas de turno y presupuesto). |
| `img/favicon.svg` | Favicon con el isotipo "BX". |
| `img/iconos/*.svg` | 12 íconos de línea propios (24×24, `stroke="currentColor"`). |
| `img/ilustraciones/*.svg` | `auto-entregado.svg` y `estado-vacio.svg` (para la fase 2). |

## 1. Paleta

Todos los colores están en `:root` como variables `--bx-*`. Los ratios son de contraste WCAG 2.x
calculados sobre los colores reales.

### Marca

| Token | Color | Uso | Contraste |
|-------|-------|-----|-----------|
| `--bx-ink` | `#0F172A` | Base oscura: barra superior, hero del inicio, footer, encabezado de tablas | Blanco sobre ink 17.9:1 |
| `--bx-ink-2` / `--bx-text` | `#1E293B` | Texto principal | 14.6:1 sobre blanco |
| `--bx-blue` | `#0052FF` | Acento de marca: botones primarios, links, íconos | 5.75:1 sobre blanco (y blanco sobre azul) |
| `--bx-blue-700` | `#0041CC` | Hover de botones, texto sobre fondos azulados | 7.2:1 sobre `--bx-blue-50` |
| `--bx-blue-300` | `#8FB0FF` | Acento sobre fondo oscuro (eyebrow del hero, íconos del footer) | 8.35:1 sobre ink |
| `--bx-blue-100` | `#DCE7FF` | Bordes al pasar el mouse, comillas decorativas | decorativo |
| `--bx-blue-50` | `#EEF3FF` | Fondo de íconos, píldoras, estado activo del menú | fondo |
| `--bx-amber` | `#F5A524` | Calidez: estrellas de las opiniones, insignia de la ilustración | decorativo (las estrellas tienen `aria-label`) |

### Neutros

| Token | Color | Uso | Contraste |
|-------|-------|-----|-----------|
| `--bx-text-soft` | `#475569` | Texto secundario (reemplaza al Titanio `#6C757D`) | 7.58 blanco · 7.10 arena · 7.24 fondo |
| `--bx-bg` | `#F8FAFC` | Fondo general y secciones "muted" | — |
| `--bx-sand` | `#FBF7F0` | Secciones alternadas cálidas y heros internos | — |
| `--bx-border` | `#E2E8F0` | Bordes de tarjetas y paneles (decorativos) | — |
| `--bx-border-field` | `#8792A2` | Borde de los campos de formulario | 3.15:1 (WCAG 1.4.11, componentes) |

### Estados

| Token | Color | Uso | Contraste |
|-------|-------|-----|-----------|
| `--bx-success` | `#047857` | Texto/íconos de éxito, insignia "Garantía" | 5.48 blanco · 5.21 sobre `#ECFDF5` |
| `--bx-success-500` | `#10B981` | Verde de marca, solo como relleno decorativo | (2.5:1, nunca para texto) |
| `--bx-danger` | `#B91C1C` | Errores de formulario, asterisco de obligatorio, botón eliminar | 6.47 blanco · 5.91 sobre `#FEF2F2` |
| `--bx-warning` | `#92400E` | Avisos | — |

Badges de estado del admin (texto oscuro sobre fondo claro, en lugar de los colores saturados de Bootstrap):
Pendiente `#78350F/#FEF3C7` (8.2:1), Confirmado `#0041CC/#EEF3FF` (7.2:1), Finalizado `#065F46/#D1FAE5` (6.8:1),
Cancelado `#334155/#E2E8F0` (8.4:1).

En el footer y la barra superior el texto es `#CBD5E1` (12:1 sobre ink) y los títulos van en blanco.
Esto corrige los contrastes que no llegaban a AA en la versión anterior (verde `#10B981` en texto,
azul sobre ink en los títulos del footer y Titanio sobre `#F8F9FA`).

## 2. Tipografía

Cargadas desde Google Fonts con `display=swap` y `preconnect`.

| Rol | Familia | Pesos | Uso |
|-----|---------|-------|-----|
| Títulos (`--bx-font-display`) | **Plus Jakarta Sans** | 600 · 700 · 800 | `h1`–`h6`, marca, eyebrows, números de pasos |
| Texto (`--bx-font-body`) | **Inter** | 400 · 500 · 600 · 700 | Cuerpo, formularios, botones, tablas |

Reemplazan a Oswald + Work Sans: Oswald es condensada y en mayúsculas se leía "dura"; Plus Jakarta Sans
tiene formas redondeadas y amables y Inter es muy legible en pantallas chicas.

| Elemento | Tamaño | Detalle |
|----------|--------|---------|
| `h1` del hero | `clamp(2.3rem, 5vw, 3.6rem)` | peso 800, interletrado −0.03em |
| `h1` interno | `clamp(2rem, 4vw, 2.85rem)` | peso 800 |
| Título de sección | `clamp(1.75rem, 3.2vw, 2.4rem)` | peso 800, centrado con bajada |
| Cuerpo | 1rem / 1.65 | nunca menos de 16 px en párrafos |
| Lead (`.bx-lead`) | 1.125rem | color `--bx-text-soft` |
| Eyebrow (`.eyebrow`) | 0.8rem mayúsculas | píldora azul clara |

## 3. Forma, espaciado y profundidad

- **Dos radios** (antes había 0.25 y 0.5 rem mezclados): `--bx-radius-control` 0.75rem para botones y
  campos, `--bx-radius-surface` 1.25rem para tarjetas, paneles e imágenes. Píldoras con `999px`.
- **Sombras suaves** en tres niveles: `--bx-shadow-sm` (reposo), `--bx-shadow-md` (paneles de formulario,
  navbar con scroll) y `--bx-shadow-lg` (hover de tarjetas, franja de confianza, menús).
- **Anillo de foco** `--bx-ring`: `0 0 0 .25rem rgba(0,82,255,.28)` en botones y campos.
- **Secciones** con `padding` de 5rem (3.5rem en móvil) alternando blanco, arena y gris muy claro.

## 4. Componentes

| Componente | Clases | Notas |
|------------|--------|-------|
| Botón primario | `.btn-bx-primary` | Azul con sombra de color; sube 2px al hover. Para la acción principal. |
| Botón secundario | `.btn-bx-outline-dark` | Borde ink sobre blanco; se rellena al hover. |
| Botón sobre fondo oscuro | `.btn-bx-ghost`, `.btn-bx-light` | Hero, banda CTA y footer. |
| Tamaños | `.btn-sm`, `.btn-lg` | Combinables con los anteriores. |
| Hero del inicio | `.bx-hero`, `.bx-hero-trust`, `.bx-hero-media` | Ink con brillo azul/ámbar, dos CTA, tres sellos de confianza e ilustración. |
| Hero interno | `.bx-page-hero` | Arena con brillo azul; lo usan todas las páginas (también login y admin). |
| Encabezado de sección | `.bx-section-head` | Eyebrow + título + bajada, centrado. |
| Tarjeta | `.bx-card` (+ `.bx-card-hover`, `.bx-card-static`) | Ícono en `.bx-card-icon`. |
| Tarjeta enlace | `.bx-card-link` + `.bx-card-cta` | Toda la tarjeta es clickeable (pseudo-elemento sobre el link), foco visible en el borde. |
| Franja de confianza | `.bx-trust-strip` + `.bx-feature` | Se superpone al hero del inicio. |
| Pasos | `.bx-steps` + `.bx-step` (o `.bx-steps--compact`) | Lista ordenada con contador CSS. |
| Testimonio | `.bx-quote`, `.bx-stars`, `.bx-avatar` | Estrellas con `role="img"` + `aria-label`; avatar con iniciales. |
| FAQ | `details.bx-faq-item` | Acordeón nativo, sin JS, accesible por teclado. |
| Galería | `.bx-gallery-card`, `.bx-gallery-media`, `.bx-tag` | Zoom suave de la imagen al hover. |
| Panel lateral | `.bx-side-panel` (+ `.bx-sticky`) | Fijo al hacer scroll en escritorio. |
| Contacto | `.bx-info-item`, `.bx-hours` | Lista de datos con ícono y tabla de horarios. |
| Banda CTA | `.bx-cta-band` | Incluida desde `footer.php` en las páginas informativas. |
| Íconos propios | `.bx-icon .bx-icon--{nombre}` | SVG como `mask-image`: heredan `currentColor`. |
| Insignia | `.bx-badge-ok`, `.badge` | Píldoras con contraste AA. |
| Alertas | `.alert-*` de Bootstrap | Ícono a la izquierda (Bootstrap Icons), borde lateral de color. |
| Tabla admin | `.bx-table` | Encabezado ink redondeado; en < 992px pasa a tarjetas apiladas con `data-label`. |

### Formularios

- Label en negrita arriba de cada campo y **asterisco rojo automático** en los obligatorios
  (`label:has(+ [required])::after`), más la nota "Los campos marcados con * son obligatorios".
  El lector de pantalla ya anuncia el atributo `required`. Los opcionales dicen "(opcional)".
- Borde de 1.5px con contraste 3:1, alto mínimo de 48px (área táctil cómoda) y radio de control.
- **Foco**: borde azul + anillo. **Error** (`.is-invalid`): borde y fondo rojos suaves, ícono de Bootstrap
  y mensaje `.invalid-feedback` con ícono; las reglas van después de la base para no pisarlas (antes el
  error quedaba gris). **Éxito** (`.is-valid`): borde verde.
- Ayudas con `.form-text` enlazadas por `aria-describedby`, y contador de caracteres en los textareas
  (`data-contador`).
- No se tocó la lógica PHP: validación, sanitización, CSRF y nombres de campos siguen iguales. Sigue
  el `novalidate` para que los errores los muestre siempre el servidor con el mismo estilo.

## 5. Movimiento y accesibilidad

- Transiciones cortas (150–300 ms): hover de botones y tarjetas, sombra de la navbar al hacer scroll,
  chevron del FAQ, zoom de la galería y aparición de bloques al entrar en pantalla (`.bx-reveal`).
- Con `prefers-reduced-motion: reduce` se desactivan todas las animaciones y el `scroll-behavior: smooth`.
  La aparición al scroll solo se aplica si hay JS (`html.js`), así sin JS el contenido se ve igual.
- **Skip-link** visible al tabular; `main.js` le agrega `tabindex="-1"` al `<main>` para que el foco
  realmente se mueva.
- **Foco visible** en todo el sitio (`:focus-visible` de 3px azul, celeste sobre fondos oscuros).
- Menú con `aria-current="page"` en la página actual además del estilo de píldora.
- Imágenes con `alt` descriptivo (se aclara cuando son ilustraciones), `width`/`height` para evitar
  saltos de layout y `loading="lazy"` en las que están fuera de la primera pantalla.
- Links externos de WhatsApp y Google Maps con `target="_blank" rel="noopener noreferrer"`.

## 6. Móvil

- El botón **"Sacar turno"** queda visible al lado del menú hamburguesa (antes solo estaba adentro).
- El menú desplegado es un panel blanco con ítems de 48px de alto.
- En el hero los CTA ocupan todo el ancho; la ilustración se achica.
- La tabla del admin se convierte en tarjetas; los botones de formularios ocupan todo el ancho.

## 7. Decisiones de contenido (auditoría UX)

| Hallazgo | Qué hice |
|----------|----------|
| Horarios distintos en footer, contacto y turnos | Unificados en barra superior, footer, contacto, inicio y FAQ: **Lun a Vie 8:00–18:00 · Sáb 8:00–13:00**. |
| Galería presentaba ilustraciones como trabajos reales | Renombrada a "Así trabajamos", con `alt` "Ilustración: ..." y aviso de que se sumarán fotos reales. |
| Precio `$45.000` fijo y desactualizado en `servicio-detalle.php` | Reemplazado por "Según diagnóstico" con explicación del presupuesto por escrito. |
| Tarjetas de servicios del inicio sin CTA | Cada tarjeta lleva a detalle, turno o presupuesto. |
| Hero solo texto | Hero con ilustración propia, sellos de confianza y CTA "Sacar turno" + "Pedir presupuesto". |
| Testimonios y FAQ sin cierre | Banda CTA compartida desde el footer; la FAQ además tiene panel "¿No encontraste tu respuesta?". |
| FAQ siempre abierta | Acordeón con `<details>`. |
| Contacto con mapa ilustrado | Se mantiene la ilustración y se agrega botón "Cómo llegar" a Google Maps. |
| Nosotros con colores inline | Misión/visión/valores en tarjetas del sistema y sección de pasos. |
| Sin favicon | `img/favicon.svg`. |

## 8. Pendientes para la fase 2 (rama integrada)

Estas páginas no se tocaron en esta fase porque cambian en otras ramas; ya heredan el nuevo estilo
desde el CSS, pero quedan mejoras de marcado:

- **`servicios.php`**: 5 de 6 botones "Ver detalle" vuelven a la misma página; el catálogo está fijo en
  el código y no coincide con la tabla `servicios` de la BD (TP9 ya lo lee de la BD). Usar
  `.bx-card-link` con íconos `.bx-icon--*` y la ilustración `img/ilustraciones/estado-vacio.svg` para
  "sin resultados".
- **`turnos.php`**: ocultar el formulario después de un envío exitoso (usar `auto-entregado.svg`),
  envolver el formulario en `.bx-form-card`, horarios del panel con `.bx-hours`, `min` de fecha en
  `includes/campos_turno.php` y marcar el grupo de servicios como un todo en vez de cada checkbox.
- **`login.php` / `registro.php`**: botón para mostrar la contraseña y requisitos de contraseña en vivo.
- **`admin/*`**: barra de filtros dentro de una tarjeta y conteo por estado.
- Opcional: datos estructurados `LocalBusiness` y metas Open Graph.

## 9. Cómo verlo

```bash
php -S localhost:8110
```

Abrir <http://localhost:8110>. Para revisar el modo sin animaciones, activar "reducir movimiento" en el
sistema operativo o en las DevTools (Rendering → `prefers-reduced-motion`).
