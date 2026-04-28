# Manual de Usuario — TrazaIPS

## 1. Acceso al Sistema

1. Abra el navegador y acceda a la URL del sistema
2. En la pantalla de login ingrese su usuario y contraseña
3. El sistema redirigirá al Dashboard según su rol

## 2. Roles y Permisos

### Administrador
- Acceso completo a todos los módulos
- Gestión de usuarios
- Reportes y auditorías

### Profesional de Salud
- Búsqueda de pacientes
- Registro de consultas médicas
- Generación y validación de recetas

### Farmacéutico
- Búsqueda de pacientes
- Gestión de stock y lotes
- Dispensación de medicamentos
- Consulta de trazabilidad

### Auditor
- Consulta de trazabilidad
- Reportes y estadísticas
- Auditorías

## 3. Flujo de Trazabilidad

### Paso 1: Identificación del Paciente
1. Ir a **Pacientes → Buscar por CI**
2. Ingresar el número de cédula
3. Verificar condición de asegurado
4. Si es asegurado, continuar al siguiente paso
5. Si no es asegurado, el sistema indicará que no puede continuar

### Paso 2: Registro de Atención Médica
1. Desde el perfil del paciente, hacer clic en **Nueva Consulta**
2. Completar diagnóstico y observaciones
3. Guardar la consulta

### Paso 3: Generación de Receta
1. Desde la consulta, generar una receta electrónica
2. Agregar medicamentos con dosis e indicaciones
3. Guardar la receta

### Paso 4: Validación de Cobertura
1. El farmacéutico o autorizado valida la receta
2. Confirmar cobertura institucional
3. La receta cambia a estado "Validada"

### Paso 5: Dispensación
1. Ir a **Dispensación → Procesar**
2. Buscar la receta validada
3. Seleccionar el lote disponible
4. Confirmar la dispensación
5. El sistema registra automáticamente los datos de trazabilidad

### Paso 6: Trazabilidad
1. Ir a **Trazabilidad → Seguimiento**
2. Buscar por paciente, medicamento o lote
3. Ver la cadena completa de trazabilidad

## 4. Gestión de Farmacia

### Ingreso de Stock
1. Ir a **Farmacia → Ingreso**
2. Seleccionar medicamento
3. Ingresar datos del lote (número, vencimiento, cantidad, proveedor)
4. Guardar

### Consulta de Stock
1. Ir a **Farmacia → Stock**
2. Buscar medicamento por nombre
3. Ver lotes disponibles y fechas de vencimiento

### Alertas
- El sistema muestra alertas de lotes próximos a vencer (90 días)
- Alertas de stock bajo (menos de 10 unidades)

## 5. Reportes

- **Consumo**: Reporte de medicamentos dispensados por período
- **Stock**: Estado actual del inventario por establecimiento
- **Auditoría**: Registro de todas las acciones del sistema
- **Trazabilidad**: Reporte completo de la cadena de trazabilidad

## 6. Exportación

Los reportes pueden exportarse en formato PDF y Excel.