# Sistema Web de Trazabilidad de Medicamentos IPS

Sistema basado en tecnologías de código abierto (PHP + MySQL) que permite el monitoreo integral de la trazabilidad de medicamentos en las dependencias del IPS a nivel nacional.

## Objetivos

**Objetivo General:** Desarrollar e implementar un sistema web basado en tecnologías de código abierto que permita el monitoreo integral de la trazabilidad de medicamentos en las dependencias del IPS a nivel nacional.

**Objetivos Específicos:**
- Analizar los flujos de información actuales entre las farmacias periféricas y el centro de distribución del IPS.
- Diseñar una base de datos robusta en MySQL que garantice la integridad y disponibilidad de los datos de trazabilidad.
- Desarrollar una interfaz web intuitiva utilizando PHP para facilitar el registro de entradas y salidas por parte del personal de salud.

## Stack Tecnológico

- **Backend:** PHP 8+ (patrón MVC)
- **Base de datos:** MySQL 8
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Dependencias:** Composer (PhpSpreadsheet, DomPDF)

## Estructura del Proyecto

```
tesis-americana-ips/
├── public/              # Punto de entrada y assets
├── app/
│   ├── config/          # Configuración (BD, constantes, roles)
│   ├── controllers/     # Controladores MVC
│   ├── models/          # Modelos de datos
│   ├── views/           # Vistas organizadas por módulo
│   ├── middleware/       # Autenticación y roles
│   └── helpers/         # Funciones auxiliares
├── database/            # Schema y seeds MySQL
├── docs/                # Documentación y diagramas
└── tests/               # Pruebas unitarias
```

## Fases del Flujograma

1. **Identificación del paciente** - Validación CI / asegurado
2. **Registro de atención médica** - Consulta SIH / receta electrónica
3. **Validación de la receta** - Cobertura y normativas
4. **Gestión de farmacia** - Stock DASM / lote / vencimiento
5. **Dispensación** - Entrega y registro de trazabilidad
6. **Cierre y trazabilidad** - Consolidación completa

## Instalación

```bash
composer install
```

Configurar archivo `.env` con los datos de conexión MySQL, luego importar `database/schema.sql` y `database/seed.sql`.

## Licencia

Proyecto académico - Tesis Americana