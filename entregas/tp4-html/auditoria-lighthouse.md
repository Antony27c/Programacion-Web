# Auditoría Lighthouse — Accesibilidad

**Proyecto:** BOXLY (Programación Web)  
**Fecha:** septiembre 2026  
**Herramienta:** Chrome DevTools → Lighthouse (categoría Accessibility)  
**Alcance:** sitio estático local (10 vistas HTML)

## Cómo repetir la auditoría

1. Servir el sitio en local (por ejemplo con Live Server o `npx serve`).
2. Abrir Chrome → DevTools → pestaña **Lighthouse**.
3. Marcar **Accessibility** (y opcionalmente Performance / Best Practices / SEO).
4. Ejecutar el informe en desktop y mobile.
5. Revisar cada vista principal: `index.html`, formularios (`turnos.html`, `presupuesto.html`, `contacto.html`) y una vista de contenido (`servicios.html`, `faq.html`).

## Correcciones aplicadas antes / durante la auditoría

| Problema habitual | Cómo se resolvió en el proyecto |
|-------------------|----------------------------------|
| Contraste insuficiente en CTAs / textos | Paleta BOXLY: texto `#1E293B` sobre `#F8F9FA`/`#FFFFFF`; CTA `#0052FF` con texto blanco |
| Enlaces o botones sin nombre accesible | Textos visibles en CTAs; `aria-label` en toggler del menú |
| Imágenes sin alternativa | Placeholders con `role="img"` + `aria-label` descriptivo |
| Formularios sin labels | Cada input tiene `<label for="...">` asociado |
| Validación solo visual | Atributos `required`, feedback Bootstrap y `novalidate` + JS de validación |
| Navegación por teclado / skip | Link “Saltar al contenido” al inicio de cada página |
| Idioma del documento | `lang="es"` en todas las vistas |
| Jerarquía de headings | Un `h1` por página; secciones con `h2`/`h3` ordenados |
| Landmark regions | Uso de `header`, `nav`, `main`, `footer` y `aria-label` en nav |
| Ítems decorativos | `aria-hidden="true"` en logo mark e iconos de cards |

## Resultados esperados / checklist

Completar con los scores reales al correr Lighthouse en tu máquina:

| Vista | Accessibility | Notas |
|-------|---------------|-------|
| `index.html` | __ / 100 | |
| `nosotros.html` | __ / 100 | |
| `servicios.html` | __ / 100 | |
| `servicio-detalle.html` | __ / 100 | |
| `turnos.html` | __ / 100 | |
| `presupuesto.html` | __ / 100 | |
| `testimonios.html` | __ / 100 | |
| `galeria.html` | __ / 100 | |
| `contacto.html` | __ / 100 | |
| `faq.html` | __ / 100 | |

## Pendientes opcionales (si Lighthouse marca avisos)

- Reemplazar placeholders por fotos reales con `alt` concreto.
- Agregar favicon y `theme-color`.
- Incluir mapa embebido accesible (iframe con `title`).
- Revisar tamaño de tap targets en mobile si el informe lo indica.

## Formato con Prettier

```bash
npm install
npm run format
```

Documenta aquí la fecha en que corriste Prettier y Lighthouse antes del Pull Request.
