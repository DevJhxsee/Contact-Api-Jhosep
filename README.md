# Contact API

[![tests](https://github.com/DevJhxsee/Contact-Api-Jhosep/actions/workflows/tests.yml/badge.svg)](https://github.com/DevJhxsee/Contact-Api-Jhosep/actions/workflows/tests.yml)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20)
![Licencia](https://img.shields.io/badge/licencia-MIT-blue)

API REST para gestionar una agenda de contactos personal: cada usuario inicia sesión
con un token y administra solo sus propios contactos.
Proyecto del programa ADSO (SENA), ficha 3174404.

- Producción: https://3174404-XX.rdsdevs.com.co/api/v1
- Versión actual: v0.1.0

## Contenido
- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Variables de entorno](#variables-de-entorno)
- [Pruebas](#pruebas)
- [Documentación de la API](#documentación-de-la-api)
- [Endpoints](#endpoints)
- [Flujo de trabajo](#flujo-de-trabajo)
- [Autor](#autor)
- [Licencia](#licencia)

## Tecnologías
PHP 8.3, Laravel 13, MySQL 8, Laravel Sanctum, Pest, Pint, Scramble.

## Requisitos
PHP 8.3+, Composer 2, MySQL 8, Git.

## Instalación
1. Crear las bases de datos en MySQL:

```sql
CREATE DATABASE db_contact_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE db_contact_api_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Clonar e instalar:

```bash
git clone https://github.com/DevJhxsee/Contact-Api-Jhosep.git
cd Contact-Api-Jhosep
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Variables de entorno
| Variable    | Ejemplo        | Descripción                        |
|-------------|----------------|------------------------------------|
| APP_NAME    | "Contact API"  | Nombre que responde /health        |
| DB_DATABASE | db_contact_api | Base de datos MySQL                |
| DB_USERNAME | root           | Usuario de MySQL                   |
| DB_PASSWORD |                | Contraseña de MySQL (nunca en Git) |
| DB_PREFIX   | jhosep_        | Prefijo de las tablas              |

## Pruebas
Las pruebas usan la base `db_contact_api_test`, nunca la de desarrollo.

```bash
php artisan test
vendor/bin/pint --test
```

## Documentación de la API
Con el servidor local: http://127.0.0.1:8000/docs/api

## Endpoints
| Método | Ruta           | Descripción         | Auth |
|--------|----------------|---------------------|------|
| GET    | /api/v1/health | Estado del servicio | No   |

## Flujo de trabajo
Git Flow (main, develop, feature/\*, release/\*, hotfix/\*),
Conventional Commits y versionado semántico.
Los cambios se registran en [CHANGELOG.md](CHANGELOG.md).

## Autor
Jhosep Suarez · aprendiz ADSO · github.com/DevJhxsee

## Licencia
Distribuido bajo la licencia MIT. Ver [LICENSE.md](LICENSE.md).