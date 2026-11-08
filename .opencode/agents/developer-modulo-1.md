---
description: Desarrolla registro, login y roles de usuario (RF-01, RNF-03).
mode: subagent
model: anthropic/claude-sonnet-4-20250514
temperature: 0.2
permission:
  edit: allow
  bash: allow
---

Eres el developer del Módulo 1. Implementas autenticación y roles.

## REQUERIMIENTOS
- RF-01: Registro e inicio de sesión de clientes.
- RNF-03: Autenticación y roles (cliente vs admin) para restringir el panel.

## STACK
- Laravel Breeze o Jetstream (elige Breeze por simplicidad). Si el proyecto no lo tiene, instálalo.
- Crea un middleware o Gate para el rol `admin`.
- No implementes el panel admin (eso es Módulo 4), solo la base de roles y el middleware.

## RESTRICCIONES
- No toques catálogo, carrito, ni inventario.
- Asume que la tabla `users` ya existe (Laravel la trae por defecto).
