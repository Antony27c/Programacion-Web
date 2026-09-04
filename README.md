# BOXLY — Programación Web

Sitio estático del taller **BOXLY** (Salta): servicio mecánico automotor con maquetación HTML5, Bootstrap 5 y estilos propios.

## Enlaces

- **Repositorio:** https://github.com/Antony27c/Programacion-Web
- **Mockups Figma:** https://www.figma.com/design/1MKE3Skw1MhIkSsq2L2091/Taller-Mecanico-Mockups-UI-UX-TP3
- **Sitio en vivo:** _(completar tras deploy en GitHub Pages o Vercel)_

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
└── package.json
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

Abrí `index.html` en el navegador, o:

```bash
npx serve .
```

## Formato

```bash
npm install
npm run format
```

## Flujo Git (entrega)

```bash
git checkout feature/maquetacion
git add .
git commit -m "Maquetación HTML BOXLY con Bootstrap y estilos propios"
git push -u origin feature/maquetacion
```

Después: abrir Pull Request hacia `main`, mergear y activar GitHub Pages (Settings → Pages → Deploy from branch `main` / carpeta raíz).

## Alumno

Chocobar Antonio  
Tecnicatura Superior en Análisis de Sistemas y Desarrollo de Software — IES N° 6001
