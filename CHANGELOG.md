# Changelog

Todos los cambios importantes de este proyecto se registran en este archivo.
El formato sigue Keep a Changelog y el proyecto usa versionado semántico.

## [Unreleased]

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
