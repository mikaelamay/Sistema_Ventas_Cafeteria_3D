# 07 — Base de datos (Especificación técnica completa)

Documento de especificación del esquema relacional del sistema de ventas para cafetería. Todas las tablas viven en la base de datos `cafeteria` (MariaDB 11.8, charset `utf8mb4`) y se crean mediante las migraciones de `database/migrations`.

---

## 2.3.1 Modelo Entidad-Relación

El siguiente diagrama `erDiagram` describe las entidades del dominio, sus atributos y las relaciones entre ellas.

```mermaid
erDiagram
    users ||--o{ pedidos : realiza
    users |o--o{ movimientos_inventario : audita
    pedidos ||--o{ detalle_pedido : contiene
    productos ||--o{ detalle_pedido : referencia
    insumos ||--o{ movimientos_inventario : registra

    users {
        bigint id PK
        varchar name UK1
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar role
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    productos {
        bigint id PK
        varchar nombre
        text descripcion
        decimal precio
        int unsigned stock
        varchar categoria
        varchar imagen_path
        varchar modelo_3d_path
        boolean activo
        timestamp created_at
        timestamp updated_at
    }

    pedidos {
        bigint id PK
        bigint user_id FK
        varchar estado
        varchar metodo_pago
        decimal total
        text notas
        timestamp created_at
        timestamp updated_at
    }

    detalle_pedido {
        bigint id PK
        bigint pedido_id FK
        bigint producto_id FK
        int unsigned cantidad
        decimal precio_unitario
        decimal subtotal
        timestamp created_at
        timestamp updated_at
    }

    insumos {
        bigint id PK
        varchar nombre
        varchar unidad_medida
        decimal stock_actual
        decimal stock_minimo
        decimal costo_unitario
        timestamp created_at
        timestamp updated_at
    }

    movimientos_inventario {
        bigint id PK
        bigint insumo_id FK
        varchar tipo
        decimal cantidad
        varchar motivo
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }
```

### Cardinalidades

| Relación | Cardinalidad | Descripción |
| --- | --- | --- |
| `users` → `pedidos` | 1:N (0:N) | Un usuario realiza cero o muchos pedidos. Los pedidos de invitado tienen `user_id = NULL`. |
| `pedidos` → `users` | N:1 (0:1) | Cada pedido pertenece a cero o un usuario. `RESTRICT` al eliminar. |
| `pedidos` → `detalle_pedido` | 1:N | Un pedido agrupa cero o muchas líneas de detalle. |
| `detalle_pedido` → `pedidos` | N:1 | Cada línea pertenece a un solo pedido. `CASCADE` al eliminar el pedido. |
| `productos` → `detalle_pedido` | 1:N | Un producto puede aparecer en cero o muchas líneas. |
| `detalle_pedido` → `productos` | N:1 | Cada línea referencia un solo producto. `RESTRICT` al eliminar. |
| `insumos` → `movimientos_inventario` | 1:N | Un insumo acumula cero o muchos movimientos. |
| `movimientos_inventario` → `insumos` | N:1 | Cada movimiento afecta a un solo insumo. `RESTRICT` al eliminar. |
| `users` → `movimientos_inventario` | 1:N (0:N) | Un usuario puede auditar cero o muchos movimientos. |
| `movimientos_inventario` → `users` | N:1 (0:1) | `user_id` es opcional (`SET NULL`); el histórico sobrevife al usuario. |

---

## 2.3.2 Modelo Relacional

A continuación se detalla cada tabla con sus columnas, tipos, restricciones y claves foráneas, junto con las cardinalidades de las relaciones.

### Tabla `users`

| Campo | Tipo | Nulo | Clave | Descripción |
| --- | --- | --- | --- | --- |
| `id` | `bigint unsigned` | NO | PK | Identificador autoincremental. |
| `name` | `varchar(255)` | NO | | Nombre visible del usuario. |
| `email` | `varchar(255)` | NO | UK | Credencial de acceso, único. |
| `email_verified_at` | `timestamp` | SÍ | | Fecha de verificación de correo. |
| `password` | `varchar(255)` | NO | | Hash de la contraseña (cast `hashed`). |
| `role` | `varchar(255)` | NO | IK | Rol (`cliente`/`admin`), default `'cliente'`. |
| `remember_token` | `varchar(100)` | SÍ | | Token "recordarme" de Laravel. |
| `created_at` | `timestamp` | SÍ | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | SÍ | | Marca de tiempo de actualización. |

Índice: `users_role_index` sobre `role`. Clave foránea: ninguna propia (es referenciada por otras tablas).

### Tabla `productos`

| Campo | Tipo | Nulo | Clave | Descripción |
| --- | --- | --- | --- | --- |
| `id` | `bigint unsigned` | NO | PK | Identificador autoincremental. |
| `nombre` | `varchar(255)` | NO | | Nombre del producto. |
| `descripcion` | `text` | SÍ | | Descripción libre del producto. |
| `precio` | `decimal(10,2)` | NO | | Precio de venta. |
| `stock` | `int unsigned` | NO | | Stock en unidades (sin negativos). |
| `categoria` | `varchar(255)` | SÍ | | Categoría libre (`bebidas`, `postres`, `comida`). |
| `imagen_path` | `varchar(255)` | SÍ | | Ruta relativa a `storage/app/public`. |
| `modelo_3d_path` | `varchar(255)` | SÍ | | Ruta relativa al modelo GLB/GLTF. |
| `activo` | `tinyint(1)` | NO | | Visible en catálogo; default `true`. |
| `created_at` | `timestamp` | SÍ | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | SÍ | | Marca de tiempo de actualización. |

Índice compuesto: `productos_activo_categoria_index` sobre `(activo, categoria)`.

### Tabla `pedidos`

| Campo | Tipo | Nulo | Clave | Descripción |
| --- | --- | --- | --- | --- |
| `id` | `bigint unsigned` | NO | PK | Identificador autoincremental. |
| `user_id` | `bigint unsigned` | SÍ | FK → `users.id` | Autor del pedido; `NULL` en pedidos de invitado. |
| `estado` | `varchar(255)` | NO | IK | Estado del pedido (default `'pendiente'`). |
| `metodo_pago` | `varchar(255)` | NO | | Método de pago (default `'efectivo'`). |
| `total` | `decimal(10,2)` | NO | | Importe total consolidado. |
| `notas` | `text` | SÍ | | Observaciones del cliente. |
| `created_at` | `timestamp` | SÍ | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | SÍ | | Marca de tiempo de actualización. |

Clave foránea: `pedidos.user_id` → `users.id` (`ON DELETE RESTRICT`). Índice: `pedidos_estado_index` sobre `estado`.

### Tabla `detalle_pedido`

| Campo | Tipo | Nulo | Clave | Descripción |
| --- | --- | --- | --- | --- |
| `id` | `bigint unsigned` | NO | PK | Identificador autoincremental. |
| `pedido_id` | `bigint unsigned` | NO | FK → `pedidos.id` | Pedido al que pertenece la línea. |
| `producto_id` | `bigint unsigned` | NO | FK → `productos.id` | Producto de la línea. |
| `cantidad` | `int unsigned` | NO | | Unidades del producto. |
| `precio_unitario` | `decimal(10,2)` | NO | | Precio snapshot al momento de la compra. |
| `subtotal` | `decimal(10,2)` | NO | | `cantidad × precio_unitario`. |
| `created_at` | `timestamp` | SÍ | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | SÍ | | Marca de tiempo de actualización. |

Claves foráneas: `detalle_pedido.pedido_id` → `pedidos.id` (`ON DELETE CASCADE`); `detalle_pedido.producto_id` → `productos.id` (`ON DELETE RESTRICT`).

### Tabla `insumos`

| Campo | Tipo | Nulo | Clave | Descripción |
| --- | --- | --- | --- | --- |
| `id` | `bigint unsigned` | NO | PK | Identificador autoincremental. |
| `nombre` | `varchar(255)` | NO | | Nombre del insumo. |
| `unidad_medida` | `varchar(255)` | NO | | Unidad (`gramos`, `mililitros`, `unidades`). |
| `stock_actual` | `decimal(10,3)` | NO | | Stock actual con 3 decimales. |
| `stock_minimo` | `decimal(10,3)` | NO | | Umbral de reposición. |
| `costo_unitario` | `decimal(10,2)` | SÍ | | Costo unitario del insumo. |
| `created_at` | `timestamp` | SÍ | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | SÍ | | Marca de tiempo de actualización. |

### Tabla `movimientos_inventario`

| Campo | Tipo | Nulo | Clave | Descripción |
| --- | --- | --- | --- | --- |
| `id` | `bigint unsigned` | NO | PK | Identificador autoincremental. |
| `insumo_id` | `bigint unsigned` | NO | FK → `insumos.id` | Insumo afectado por el movimiento. |
| `tipo` | `varchar(255)` | NO | | Tipo (`entrada`, `salida`, `ajuste`). |
| `cantidad` | `decimal(10,3)` | NO | | Magnitud del movimiento. |
| `motivo` | `varchar(255)` | SÍ | | Justificación opcional. |
| `user_id` | `bigint unsigned` | SÍ | FK → `users.id` | Responsable del registro; `SET NULL`. |
| `created_at` | `timestamp` | SÍ | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | SÍ | | Marca de tiempo de actualización. |

Claves foráneas: `movimientos_inventario.insumo_id` → `insumos.id` (`ON DELETE RESTRICT`); `movimientos_inventario.user_id` → `users.id` (`ON DELETE SET NULL`). Índice compuesto: `(insumo_id, created_at)`.

### Tablas de infraestructura de Laravel

Además de las tablas de negocio, las migraciones base crean tablas de soporte del framework:

| Tabla | Propósito |
| --- | --- |
| `password_reset_tokens` | Tokens de recuperación de contraseña (Breeze). |
| `sessions` | Almacenamiento de sesiones (`SESSION_DRIVER=database`). |
| `cache` / `cache_locks` | Caché y bloqueos (`CACHE_STORE=database`). |
| `jobs` / `job_batches` / `failed_jobs` | Cola de trabajos (`QUEUE_CONNECTION=database`). |

---

## 2.3.3 Normalización

El esquema cumple con las tres primeras formas normales (1FN, 2FN y 3FN). A continuación se justifica cada una.

### 1FN — Primera Forma Normal

Una relación está en 1FN cuando todos sus dominios son atómicos (no contiene valores multivalor ni grupos repetidos) y cada tupla es única.

- Todas las columnas almacenan valores atómicos: no hay columnas con listas, conjuntos ni estructuras anidadas. Campos como `detalle_pedido` no replican datos del pedido ni del producto; cada fila es una línea independiente.
- Cada tabla tiene una clave primaria (`id`, `bigint unsigned` autoincremental) que garantiza la unicidad de cada tupla.
- No existen grupos repetidos ni columnas numeradas (por ejemplo, no hay `producto_1`, `producto_2`, etc.).

**Conclusión:** Todas las relaciones del esquema están en 1FN.

### 2FN — Segunda Forma Normal

Una relación está en 2FN cuando está en 1FN y cada atributo no clave depende totalmente de toda la clave primaria (no hay dependencias parciales de una parte de una clave compuesta).

- Todas las claves primarias son simples (una sola columna `id`), por lo que no puede existir dependencia parcial de una parte de una clave compuesta.
- Cada atributo no clave de cada tabla depende de la totalidad de su clave primaria (`id`).

**Conclusión:** Al no existir claves compuestas, el esquema cumple trivialmente con 2FN.

### 3FN — Tercera Forma Normal

Una relación está en 3FN cuando está en 2FN y no existen dependencias transitivas entre atributos no clave (ningún atributo no clave depende de otro atributo no clave).

- En `users`, `role`, `name`, `email`, etc. dependen directamente de `id` (la clave), no entre sí.
- En `productos`, `nombre`, `precio`, `stock`, `categoria`, etc. dependen directamente de `id`.
- En `pedidos`, `total`, `estado`, `metodo_pago`, `user_id` dependen directamente de `id`.
- En `detalle_pedido`, `subtotal` depende de `cantidad` y `precio_unitario`, pero estos últimos son atributos de la propia línea (parte de la clave de la relación de hecho), y el valor se guarda como snapshot histórico; no hay una dependencia transitiva que infrinja 3FN porque `subtotal` es un atributo derivado almacenado a propósito para conservar el histórico del importe pagado.
- En `insumos` y `movimientos_inventario`, todos los atributos no clave dependen directamente de su respectiva `id`.

**Conclusión:** No hay dependencias transitivas entre atributos no clave; el esquema cumple con 3FN.

**Resumen:** El esquema relacional está en 1FN, 2FN y 3FN.

---

## 2.3.4 Modelo Físico

A continuación se extrae el código SQL equivalente a las migraciones de Laravel que crean el esquema de negocio. El motor es InnoDB con charset `utf8mb4` y collation `utf8mb4_unicode_ci`.

### 2.3.4.1 Script SQL de las tablas de negocio

```sql
-- ============================================
-- Tabla: users (base de autenticación de Laravel)
-- ============================================
CREATE TABLE `users` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `remember_token` varchar(100) NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Columna role (migración 2026_11_08_160119_add_role_to_users_table)
ALTER TABLE `users`
    ADD `role` varchar(255) NOT NULL DEFAULT 'cliente' AFTER `password`,
    ADD INDEX `users_role_index` (`role`);

-- ============================================
-- Tabla: productos
-- ============================================
CREATE TABLE `productos` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `nombre` varchar(255) NOT NULL,
    `descripcion` text NULL DEFAULT NULL,
    `precio` decimal(10,2) NOT NULL,
    `stock` int unsigned NOT NULL DEFAULT 0,
    `categoria` varchar(255) NULL DEFAULT NULL,
    `imagen_path` varchar(255) NULL DEFAULT NULL,
    `modelo_3d_path` varchar(255) NULL DEFAULT NULL,
    `activo` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `productos_activo_categoria_index` (`activo`, `categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: pedidos
-- ============================================
CREATE TABLE `pedidos` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL,
    `estado` varchar(255) NOT NULL DEFAULT 'pendiente',
    `total` decimal(10,2) NOT NULL DEFAULT 0,
    `notas` text NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `pedidos_estado_index` (`estado`),
    CONSTRAINT `pedidos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modificación: user_id nullable + metodo_pago (migración 2026_11_08_163735)
ALTER TABLE `pedidos`
    MODIFY `user_id` bigint unsigned NULL DEFAULT NULL,
    ADD `metodo_pago` varchar(255) NOT NULL DEFAULT 'efectivo' AFTER `estado`;

-- ============================================
-- Tabla: detalle_pedido
-- ============================================
CREATE TABLE `detalle_pedido` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `pedido_id` bigint unsigned NOT NULL,
    `producto_id` bigint unsigned NOT NULL,
    `cantidad` int unsigned NOT NULL,
    `precio_unitario` decimal(10,2) NOT NULL,
    `subtotal` decimal(10,2) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `detalle_pedido_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `detalle_pedido_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: insumos
-- ============================================
CREATE TABLE `insumos` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `nombre` varchar(255) NOT NULL,
    `unidad_medida` varchar(255) NOT NULL,
    `stock_actual` decimal(10,3) NOT NULL DEFAULT 0,
    `stock_minimo` decimal(10,3) NOT NULL DEFAULT 0,
    `costo_unitario` decimal(10,2) NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: movimientos_inventario
-- ============================================
CREATE TABLE `movimientos_inventario` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `insumo_id` bigint unsigned NOT NULL,
    `tipo` varchar(255) NOT NULL,
    `cantidad` decimal(10,3) NOT NULL,
    `motivo` varchar(255) NULL DEFAULT NULL,
    `user_id` bigint unsigned NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `movimientos_inventario_insumo_id_created_at_index` (`insumo_id`, `created_at`),
    CONSTRAINT `movimientos_inventario_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `movimientos_inventario_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.3.4.2 Diccionario de Datos

A continuación se presenta el diccionario de datos consolidado de todas las tablas de negocio.

| Campo | Tabla | Tipo de Dato | Nulo | Descripción |
| --- | --- | --- | --- | --- |
| `id` | users | `bigint unsigned` | NO | Clave primaria autoincremental. |
| `name` | users | `varchar(255)` | NO | Nombre visible del usuario. |
| `email` | users | `varchar(255)` | NO | Correo electrónico, credencial de acceso (único). |
| `email_verified_at` | users | `timestamp` | SÍ | Fecha y hora de verificación del correo. |
| `password` | users | `varchar(255)` | NO | Hash de la contraseña (cast `hashed`). |
| `role` | users | `varchar(255)` | NO | Rol del usuario (`cliente`, `admin`); default `'cliente'`. |
| `remember_token` | users | `varchar(100)` | SÍ | Token de "recordarme" de Laravel. |
| `created_at` | users | `timestamp` | SÍ | Marca de tiempo de creación del registro. |
| `updated_at` | users | `timestamp` | SÍ | Marca de tiempo de última actualización. |
| `id` | productos | `bigint unsigned` | NO | Clave primaria autoincremental. |
| `nombre` | productos | `varchar(255)` | NO | Nombre del producto. |
| `descripcion` | productos | `text` | SÍ | Descripción libre del producto. |
| `precio` | productos | `decimal(10,2)` | NO | Precio de venta en moneda local. |
| `stock` | productos | `int unsigned` | NO | Cantidad en unidades disponibles. |
| `categoria` | productos | `varchar(255)` | SÍ | Categoría del producto. |
| `imagen_path` | productos | `varchar(255)` | SÍ | Ruta relativa a `storage/app/public` de la imagen. |
| `modelo_3d_path` | productos | `varchar(255)` | SÍ | Ruta relativa al modelo 3D (GLB/GLTF). |
| `activo` | productos | `tinyint(1)` | NO | Indica si el producto es visible en el catálogo. |
| `created_at` | productos | `timestamp` | SÍ | Marca de tiempo de creación. |
| `updated_at` | productos | `timestamp` | SÍ | Marca de tiempo de última actualización. |
| `id` | pedidos | `bigint unsigned` | NO | Clave primaria autoincremental. |
| `user_id` | pedidos | `bigint unsigned` | SÍ | FK → `users.id`; `NULL` para pedidos de invitado. |
| `estado` | pedidos | `varchar(255)` | NO | Estado del pedido (default `'pendiente'`). |
| `metodo_pago` | pedidos | `varchar(255)` | NO | Método de pago (default `'efectivo'`). |
| `total` | pedidos | `decimal(10,2)` | NO | Importe total consolidado del pedido. |
| `notas` | pedidos | `text` | SÍ | Observaciones del cliente. |
| `created_at` | pedidos | `timestamp` | SÍ | Marca de tiempo de creación. |
| `updated_at` | pedidos | `timestamp` | SÍ | Marca de tiempo de última actualización. |
| `id` | detalle_pedido | `bigint unsigned` | NO | Clave primaria autoincremental. |
| `pedido_id` | detalle_pedido | `bigint unsigned` | NO | FK → `pedidos.id` (CASCADE). |
| `producto_id` | detalle_pedido | `bigint unsigned` | NO | FK → `productos.id` (RESTRICT). |
| `cantidad` | detalle_pedido | `int unsigned` | NO | Unidades del producto en la línea. |
| `precio_unitario` | detalle_pedido | `decimal(10,2)` | NO | Precio snapshot al momento de la compra. |
| `subtotal` | detalle_pedido | `decimal(10,2)` | NO | `cantidad × precio_unitario`. |
| `created_at` | detalle_pedido | `timestamp` | SÍ | Marca de tiempo de creación. |
| `updated_at` | detalle_pedido | `timestamp` | SÍ | Marca de tiempo de última actualización. |
| `id` | insumos | `bigint unsigned` | NO | Clave primaria autoincremental. |
| `nombre` | insumos | `varchar(255)` | NO | Nombre del insumo. |
| `unidad_medida` | insumos | `varchar(255)` | NO | Unidad de medida (gramos, mililitros, unidades). |
| `stock_actual` | insumos | `decimal(10,3)` | NO | Stock actual con tres decimales. |
| `stock_minimo` | insumos | `decimal(10,3)` | NO | Umbral mínimo de reposición. |
| `costo_unitario` | insumos | `decimal(10,2)` | SÍ | Costo unitario del insumo. |
| `created_at` | insumos | `timestamp` | SÍ | Marca de tiempo de creación. |
| `updated_at` | insumos | `timestamp` | SÍ | Marca de tiempo de última actualización. |
| `id` | movimientos_inventario | `bigint unsigned` | NO | Clave primaria autoincremental. |
| `insumo_id` | movimientos_inventario | `bigint unsigned` | NO | FK → `insumos.id` (RESTRICT). |
| `tipo` | movimientos_inventario | `varchar(255)` | NO | Tipo de movimiento (`entrada`, `salida`, `ajuste`). |
| `cantidad` | movimientos_inventario | `decimal(10,3)` | NO | Magnitud del movimiento. |
| `motivo` | movimientos_inventario | `varchar(255)` | SÍ | Justificación opcional del movimiento. |
| `user_id` | movimientos_inventario | `bigint unsigned` | SÍ | FK → `users.id` (SET NULL); responsable del registro. |
| `created_at` | movimientos_inventario | `timestamp` | SÍ | Marca de tiempo de creación. |
| `updated_at` | movimientos_inventario | `timestamp` | SÍ | Marca de tiempo de última actualización. |

---

## Referencias cruzadas

- [01-base-de-datos.md](01-base-de-datos.md) — Documento de referencia del esquema relacional, diccionario de tablas ampliado y relaciones Eloquent.
- [02-roles-y-autenticacion.md](02-roles-y-autenticacion.md) — Autenticación, roles y protección del panel `/admin`.
- [03-catalogo-y-visor-3d.md](03-catalogo-y-visor-3d.md) — Catálogo público, ficha de producto y visor Three.js.
- [04-carrito-y-pedidos.md](04-carrito-y-pedidos.md) — Carrito de sesión, checkout transaccional y pedidos.
- [05-administracion.md](05-administracion.md) — Panel de administración, CRUD de productos y gestión de pedidos.
- [06-inventario-y-reportes.md](06-inventario-y-reportes.md) — Inventario de insumos y reportes de ventas.
