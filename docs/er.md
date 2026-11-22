# Modelo de datos de Contact API

```mermaid
erDiagram
    USERS ||--o{ CONTACTS : "tiene (user_id)"

    USERS {
        bigint id PK
        string first_name
        string last_name "opcional"
        string email UK
        string password "hash"
        string phone_number UK "opcional"
        timestamp created_at
        timestamp updated_at
    }

    CONTACTS {
        bigint id PK
        bigint user_id FK
        string first_name
        string last_name "opcional"
        string phone_number "único por usuario"
        string email "único por usuario"
        string address
        string avatar_url "opcional"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "soft delete"
    }
```

- Un usuario tiene muchos contactos; cada contacto pertenece a un solo usuario.
- Si se elimina un usuario, se eliminan sus contactos (`cascadeOnDelete`).
- En la tabla users, el email y el teléfono son únicos.
- En la tabla contacts, el email y el teléfono son únicos **dentro de la agenda de cada usuario** (`user_id + email` y `user_id + phone_number`): dos usuarios distintos sí pueden tener el mismo contacto. El nombre, el apellido y la dirección se pueden repetir.
- Al borrar un contacto se usa soft delete: se llena `deleted_at` y la fila se conserva.
