# BOXLY — Programación Web

Sitio estático del taller **BOXLY** (Salta): servicio mecánico automotor con maquetación HTML5, Bootstrap 5 y estilos propios.

## Enlaces

- **Repositorio:** https://github.com/Antony27c/Programacion-Web
- **Mockups Figma:** https://www.figma.com/design/1MKE3Skw1MhIkSsq2L2091/Taller-Mecanico-Mockups-UI-UX-TP3
- **Sitio en vivo:** https://antony27c.github.io/Programacion-Web/

## Estructura

```
├── index.html
├── nosotros.html
├── servicios.html
├── servicio-detalle.html
├── turnos.html
├── presupuesto.html
├── testimonios.html
├── galeria.html
├── contacto.html
├── faq.html
├── css/styles.css
├── js/main.js
├── img/
├── Mockups UI_UX TP3/
├── auditoria-lighthouse.md
├── package.json
└── clase5/              ← TP5: sitio en PHP con formularios POST/GET
```

## Vistas maquetadas (10)

1. Inicio  
2. Nosotros  
3. Servicios  
4. Servicio detalle (Frenos y suspensión)  
5. Agendar turno  
6. Presupuesto  
7. Testimonios  
8. Galería  
9. Contacto  
10. FAQ  

## Tecnología

- HTML5 semántico  
- Bootstrap 5.3 (CDN)  
- CSS personalizado (paleta BOXLY)  
- JavaScript (nav activa + validación de formularios)  
- Prettier  

## Paleta

| Token | Hex | Uso |
|-------|-----|-----|
| Ink | `#0F172A` | Header, footer, hero |
| Texto | `#1E293B` | Tipografía principal |
| Acento | `#0052FF` | CTA, marca, links |
| Titanio | `#6C757D` | Texto secundario |
| Fondo | `#F8F9FA` | Superficies |
| Éxito | `#10B981` | Confirmaciones |

## Cómo verlo en local

Abrí `index.html` en el navegador:


## Clase 5 — Procesamiento de Formularios y Seguridad Web (PHP)

Carpeta: [`clase5/`](clase5/) — el sitio completo migrado a **PHP** con estructura modular
(`includes/header.php`, `includes/footer.php`, `includes/funciones.php`) y lógica de
servidor para capturar y procesar datos del navegador.

### Formulario POST — Solicitud de turno (`turnos.php`)

Captura nombre, teléfono, vehículo, servicio, fecha, horario y síntoma. Se procesa en el
mismo script (`method="post"`). Además se procesan con el mismo patrón los formularios de
`contacto.php` (nombre, email, mensaje) y `presupuesto.php`.

- Sanitización estricta de `$_POST` con `trim()` + `htmlspecialchars()` (helper `e()` y
  `limpiar()` en `includes/funciones.php`) antes de cualquier salida → previene **XSS**.
- Validación en servidor: campos obligatorios (`empty()`/`isset()`), teléfono con regex,
  fecha válida y no pasada, opciones contra listas blancas, largo mínimo de texto y
  **formato de email** con `filter_var(..., FILTER_VALIDATE_EMAIL)` en `contacto.php` y
  `presupuesto.php`.
- Retroalimentación: `alert-success` con resumen sanitizado del envío, o `alert-danger` +
  `is-invalid`/`invalid-feedback` por campo.
- Persistencia: ante error, los valores ya cargados se re-imprimen escapados en el form.

### Formulario GET — Buscador de servicios (`servicios.php?q=`)

`method="get"` filtra el catálogo de servicios en servidor (`?q=frenos`), ideal para
búsquedas compartibles por URL. El parámetro se toma con `filter_input(INPUT_GET, ...)`,
se filtra con `mb_stripos()` y todo resultado se imprime escapado. Muestra
«N resultado(s) para ...» o alerta de sin resultados.

### Cómo correrlo (XAMPP o servidor embebido)

```bash
# Con XAMPP: copiar clase5/ a htdocs o usar el servidor embebido de PHP:
cd clase5
php -S localhost:8099
# abrir http://localhost:8099
```

### Capturas

| Captura | Qué muestra |
|---------|-------------|
| `screenshots/get-busqueda.png` | Buscador GET con resultados filtrados |
| `screenshots/get-sin-resultados.png` | GET sin coincidencias + término escapado |
| `screenshots/post-errores.png` | POST con errores de validación y campos persistidos |
| `screenshots/post-exito.png` | POST exitoso con resumen de datos sanitizados |
| `screenshots/xss-sanitizado.png` | Intento `<script>` neutralizado en la salida |

## Alumno

Chocobar Antonio  
Tecnicatura Superior en Análisis de Sistemas y Desarrollo de Software — IES N° 6001
