# Índice de Documentación Generada

Este documento consolida todos los recursos de documentación generados para el proyecto **Sistema de Ventas para Cafetería**: PDFs de cada módulo y capturas de pantalla de la interfaz de usuario.

## 1. PDFs Generados

Cada documento Markdown de `/docs` ha sido convertido a PDF usando `md-to-pdf` con la hoja de estilos `/docs/pdf-styles.css`. Todos los PDFs se encuentran en la carpeta `/docs/pdf/`.

| # | Documento | PDF | Contenido |
|---|-----------|-----|-----------|
| 1 | `README.md` | [`pdf/README.pdf`](pdf/README.pdf) | Índice general, stack, puesta en marcha y estado de los módulos. |
| 2 | `01-base-de-datos.md` | [`pdf/01-base-de-datos.pdf`](pdf/01-base-de-datos.pdf) | Esquema relacional, diccionario de tablas, claves foráneas y relaciones Eloquent. |
| 3 | `02-roles-y-autenticacion.md` | [`pdf/02-roles-y-autenticacion.pdf`](pdf/02-roles-y-autenticacion.pdf) | Flujo de autenticación con Breeze, sistema de roles, middleware, rutas protegidas y credenciales semilla. |
| 4 | `03-catalogo-y-visor-3d.md` | [`pdf/03-catalogo-y-visor-3d.pdf`](pdf/03-catalogo-y-visor-3d.pdf) | Catálogo público, ficha de producto, visor Three.js y atributos `data-*`. |
| 5 | `04-carrito-y-pedidos.md` | [`pdf/04-carrito-y-pedidos.pdf`](pdf/04-carrito-y-pedidos.pdf) | Carrito en sesión, checkout transaccional, validación de stock y confirmación firmada. |
| 6 | `05-administracion.md` | [`pdf/05-administracion.pdf`](pdf/05-administracion.pdf) | Panel de administración, CRUD de productos con modelos 3D y gestión de pedidos. |
| 7 | `06-inventario-y-reportes.md` | [`pdf/06-inventario-y-reportes.pdf`](pdf/06-inventario-y-reportes.pdf) | Inventario de insumos, movimientos de stock y reportes de ventas. |
| 8 | `07-base-de-datos.md` | [`pdf/07-base-de-datos.pdf`](pdf/07-base-de-datos.pdf) | Especificación técnica completa: ER, normalización 1FN/2FN/3FN, SQL físico y diccionario de datos. |

> **Hoja de estilos:** Todos los PDFs usan `/docs/pdf-styles.css` (tipografía Inter/Helvetica, márgenes amplios, títulos en ámbar/café, diagramas Mermaid de alta calidad, paginación A4 con pie de página).

---

## 2. Capturas de Pantalla de la UI

Las capturas se tomaron con **Playwright + Chromium (headless)**, viewport **1440×900**, sin cursor del ratón, contra el servidor en `http://127.0.0.1:8000`. Todas las imágenes están en `/docs/capturas/`.

| # | Pantalla | Imagen | URL | Sesión |
|---|----------|--------|-----|--------|
| 1 | Catálogo público | [`capturas/01-catalogo-publico.png`](capturas/01-catalogo-publico.png) | `GET /` | Público (invitado) |
| 2 | Detalle del producto (Visor 3D) | [`capturas/02-detalle-producto-visor3d.png`](capturas/02-detalle-producto-visor3d.png) | `GET /productos/2` | Público (invitado) |
| 3 | Panel de Administración (Dashboard) | [`capturas/03-panel-admin-dashboard.png`](capturas/03-panel-admin-dashboard.png) | `GET /admin` | Admin (`admin@cafeteria.test`) |
| 4 | Inventario | [`capturas/04-inventario.png`](capturas/04-inventario.png) | `GET /admin/insumos` | Admin |
| 5 | Reportes de ventas | [`capturas/05-reportes-ventas.png`](capturas/05-reportes-ventas.png) | `GET /admin/reportes/ventas-periodo` | Admin |

> **Credenciales de administración:** `admin@cafeteria.test` / `password` (cuenta semilla creada por `AdminUserSeeder`). Las sesiones de administrador se gestionaron mediante Playwright (`storageState`) antes de navegar a las rutas protegidas (`/admin/*`).

---

## 3. Estructura de Archivos

```
/docs/
├── INDICE.md                          # Este archivo
├── README.md
├── 01-base-de-datos.md
├── 02-roles-y-autenticacion.md
├── 03-catalogo-y-visor-3d.md
├── 04-carrito-y-pedidos.md
├── 05-administracion.md
├── 06-inventario-y-reportes.md
├── 07-base-de-datos.md
├── pdf-styles.css
├── pdf/
│   ├── README.pdf
│   ├── 01-base-de-datos.pdf
│   ├── 02-roles-y-autenticacion.pdf
│   ├── 03-catalogo-y-visor-3d.pdf
│   ├── 04-carrito-y-pedidos.pdf
│   ├── 05-administracion.pdf
│   ├── 06-inventario-y-reportes.pdf
│   └── 07-base-de-datos.pdf
└── capturas/
    ├── 01-catalogo-publico.png
    ├── 02-detalle-producto-visor3d.png
    ├── 03-panel-admin-dashboard.png
    ├── 04-inventario.png
    └── 05-reportes-ventas.png
```

---

## 4. Resumen de Resultados

| Tarea | Estado | Detalle |
|-------|--------|---------|
| PDFs generados | ✅ Completado | 8 PDFs en `/docs/pdf/` (uno por cada `.md`) |
| Capturas generadas | ✅ Completado | 5 PNG en `/docs/capturas/` (catálogo, detalle 3D, admin dashboard, inventario, reportes) |
| Índice consolidado | ✅ Completado | `docs/INDICE.md` con enlaces a todos los recursos |
