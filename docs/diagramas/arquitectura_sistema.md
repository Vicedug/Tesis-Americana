# Arquitectura del Sistema

## Patrón MVC Simplificado (PHP Puro)

```
Browser Request
      │
      ↓
public/index.php (Router)
      │
      ├── URL Mapping (controllerMap)
      │
      ↓
Controller (app/controllers/)
      │
      ├── AuthMiddleware::check()
      ├── RoleMiddleware::requirePermission()
      │
      ├── Model (app/models/) ←── Database (MySQL via PDO)
      │     │                        ↑
      │     └── Business Logic          │
      │                                │
      ↓                                │
View (app/views/)                    │
      │                              │
      └── app_layout.php ─────────────┘
```

## Capas del Sistema

### 1. Capa de Presentación
- HTML5 + CSS3 + JavaScript
- Bootstrap 5 (grid, responsive)
- Font Awesome (iconos)
- jQuery/AJAX (búsquedas asíncronas)

### 2. Capa de Lógica (PHP)
- Controllers: Manejo de requests, validación CSRF, autorización
- Models: ORM simplificado con CRUD, relaciones, paginación
- Middleware: Autenticación y roles
- Helpers: Session, Flash, Redirect

### 3. Capa de Datos (MySQL)
- 12 tablas principales
- Foreign keys con CASCADE
- Índices optimizados
- Soft delete (deleted_at)

## Flujo de una Petición HTTP

```
1. Usuario accede a /paciente/buscar?ci=1234567
2. public/index.php recibe la URL
3. Router mapea a PacienteController::buscar()
4. AuthMiddleware::check() verifica sesión
5. RoleMiddleware::requirePermission('paciente') verifica rol
6. Controller invoca Paciente::findByCI('1234567')
7. Model ejecuta query SQL via PDO
8. Controller pasa datos a la vista
9. Vista renderiza HTML con datos
10. Response enviado al navegador
```

## Roles y Permisos

| Módulo | Administrador | Profesional Salud | Farmacéutico | Auditor |
|--------|:---:|:---:|:---:|:---:|
| Dashboard | ✓ | ✓ | ✓ | ✓ |
| Pacientes | ✓ | ✓ | ✓ | ✗ |
| Consultas | ✓ | ✓ | ✗ | ✗ |
| Recetas | ✓ | ✓ | ✗ | ✗ |
| Farmacia | ✓ | ✗ | ✓ | ✗ |
| Dispensación | ✓ | ✗ | ✓ | ✗ |
| Trazabilidad | ✓ | ✗ | ✓ | ✓ |
| Reportes | ✓ | ✗ | ✗ | ✓ |
| Usuarios | ✓ | ✗ | ✗ | ✗ |

## Seguridad

- Contraseñas hasheadas con bcrypt (PASSWORD_BCRYPT)
- Protección CSRF en todos los formularios POST
- Validación de entrada en servidor
- Prepared statements (PDO) para prevenir SQL injection
- Control de acceso por roles (RBAC)
- Sesiones con lifetime configurable