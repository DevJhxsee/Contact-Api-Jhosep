# Changelog

Todos los cambios importantes de este proyecto se registran en este archivo.
El formato sigue Keep a Changelog y el proyecto usa versionado semántico.

## [Unreleased]
### Added
- CRUD de contactos: listar, crear, ver, actualizar y eliminar (/api/v1/contacts)
- Perfil del contacto con ContactResource
- Política para que cada usuario solo use sus propios contactos (404 si es ajeno)
- Validación de contactos con mensajes en español
- Eliminación con soft delete
- Respuesta 404 en JSON con mensaje en español
- CRUD de contactos en la colección de Insomnia
### Changed
- El email y el teléfono de los contactos son únicos por agenda de usuario, no en toda la tabla

## [0.3.0] - 2026-11-17
### Added
- Login con token de Sanctum (POST /api/v1/auth/login)
- Perfil del usuario autenticado (GET /api/v1/auth/me)
- Cierre de sesión que revoca el token (POST /api/v1/auth/logout)
- Respuesta 401 en JSON con el enlace al login cuando falta el token
- Límite de 5 intentos de login por minuto
- Esquema Bearer en la documentación
- Login, perfil y cierre de sesión en la colección de Insomnia
### Changed
- El endpoint de salud pasa a un controlador invocable
- Requisito de PHP actualizado a 8.4 o superior en el README

## [0.2.0] - 2026-11-17
### Added
- Campos first_name, last_name y phone_number (único) en usuarios
- Tabla contacts con llave foránea a users, email y teléfono únicos y soft delete
- Modelos User y Contact con sus relaciones
- Factories y seeder con datos de ejemplo
- Pruebas del modelo de datos
- Diagrama entidad-relación en docs/er.md

## [0.1.0] - 2026-11-12
### Added
- GET /api/v1/health
- Errores en JSON para rutas /api
- Documentación OpenAPI con Scramble en /docs/api
- Colección de Insomnia en docs/insomnia con entornos local y prod
