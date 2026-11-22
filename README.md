# Contact API

[![tests](https://github.com/DevJhxsee/Contact-Api-Jhosep/actions/workflows/tests.yml/badge.svg)](https://github.com/DevJhxsee/Contact-Api-Jhosep/actions/workflows/tests.yml)
![PHP](https://img.shields.io/badge/PHP-8.4%2B-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20)
![Licencia](https://img.shields.io/badge/licencia-MIT-blue)

API REST para gestionar una agenda de contactos personal: cada usuario inicia sesión
con un token y administra solo sus propios contactos.
Proyecto del programa ADSO (SENA), ficha 3174404.

- Producción: https://3174404-XX.rdsdevs.com.co/api/v1
- Versión actual: v1.0.0

## Contenido
- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Modelo de datos](#modelo-de-datos)
- [Datos de prueba](#datos-de-prueba)
- [Variables de entorno](#variables-de-entorno)
- [Pruebas](#pruebas)
- [Documentación de la API](#documentación-de-la-api)
- [Endpoints](#endpoints)
- [Reglas de los contactos](#reglas-de-los-contactos)
- [Autenticación](#autenticación)
- [Flujo de trabajo](#flujo-de-trabajo)
- [Autor](#autor)
- [Licencia](#licencia)

## Tecnologías
PHP 8.5, Laravel 13, MySQL 8, Laravel Sanctum, Pest, Pint, Scramble.

## Requisitos
PHP 8.4+, Composer 2, MySQL 8, Git.

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

## Modelo de datos
Diagrama entidad-relación: [docs/er.md](docs/er.md)

## Datos de prueba
```bash
php artisan migrate:fresh --seed
```

Crea un usuario demo (demo@contactapi.test / password) con 10 contactos,
y 2 usuarios más con 5 contactos cada uno. Solo para desarrollo local.

## Variables de entorno
| Variable    | Ejemplo        | Descripción                        |
|-------------|----------------|------------------------------------|
| APP_NAME    | "Contact API"  | Nombre que responde /health        |
| DB_DATABASE | db_contact_api | Base de datos MySQL                |
| DB_USERNAME | root           | Usuario de MySQL                   |
| DB_PASSWORD |                | Contraseña de MySQL (nunca en Git) |
| DB_PREFIX   | j_             | Prefijo de las tablas              |

## Pruebas
Las pruebas usan la base `db_contact_api_test`, nunca la de desarrollo.

```bash
php artisan test
vendor/bin/pint --test
```

## Documentación de la API
Con el servidor local: http://127.0.0.1:8000/docs/api

## Endpoints
| Método | Ruta                  | Descripción                       | Auth   |
|--------|-----------------------|-----------------------------------|--------|
| GET    | /api/v1/health        | Estado del servicio               | No     |
| POST   | /api/v1/auth/login    | Inicia sesión y entrega un token  | No     |
| GET    | /api/v1/auth/me       | Perfil del usuario autenticado    | Bearer |
| POST   | /api/v1/auth/logout   | Cierra sesión y revoca el token   | Bearer |
| GET    | /api/v1/contacts      | Lista mis contactos (paginados)   | Bearer |
| POST   | /api/v1/contacts      | Crea un contacto                  | Bearer |
| GET    | /api/v1/contacts/{id} | Perfil de un contacto             | Bearer |
| PUT    | /api/v1/contacts/{id} | Actualiza un contacto             | Bearer |
| DELETE | /api/v1/contacts/{id} | Elimina un contacto (soft delete) | Bearer |

## Reglas de los contactos
- Cada usuario solo ve y modifica sus propios contactos. El contacto de otro usuario responde `404`,
  igual que uno inexistente, para no confirmar que existe en otra agenda.
- Un contacto que no existe, o que fue eliminado, responde `404` en JSON.
- El email y el teléfono no se pueden repetir dentro de la agenda de un usuario (`422`);
  el nombre, el apellido y la dirección sí se pueden repetir.
- Al eliminar se usa soft delete: el contacto se marca con `deleted_at` y deja de aparecer.

## Autenticación
1. `POST /api/v1/auth/login` con `email`, `password` y `device_name`.
2. Guardar el `token` de la respuesta.
3. Enviar en cada petición protegida:

```
Authorization: Bearer {token}
Accept: application/json
```

Sin token, la API responde `401` en JSON con el enlace al login (`login_url`).
El login admite 5 intentos por minuto por correo e IP.

## Flujo de trabajo
Git Flow (main, develop, feature/\*, release/\*, hotfix/\*),
Conventional Commits y versionado semántico.
Los cambios se registran en [CHANGELOG.md](CHANGELOG.md).

## Autor
Jhosep Suarez · aprendiz ADSO · github.com/DevJhxsee

## Licencia
Distribuido bajo la licencia MIT. Ver [LICENSE.md](LICENSE.md).