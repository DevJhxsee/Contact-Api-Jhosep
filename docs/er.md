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
        string phone_number UK
        string email UK
        string address
        string avatar_url "opcional"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "soft delete"
    }
```

- Un usuario tiene muchos contactos; cada contacto pertenece a un solo usuario.
- Si se elimina un usuario, se eliminan sus contactos (`cascadeOnDelete`).
- El email y el teléfono no se pueden repetir. El nombre, el apellido y la dirección sí.
- Al borrar un contacto se usa soft delete: se llena `deleted_at` y la fila se conserva.
