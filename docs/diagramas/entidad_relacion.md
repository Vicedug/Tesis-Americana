# Diagrama Entidad-Relación

## Tablas Principales

```
establecimientos (1) ──── (N) usuarios
establecimientos (1) ──── (N) pacientes
establecimientos (1) ──── (N) lotes
establecimientos (1) ──── (N) dispensaciones
establecimientos (1) ──── (N) movimientos_stock

pacientes (1) ──── (N) consultas
usuarios (1) ──── (N) consultas [profesional_id]
establecimientos (1) ──── (N) consultas

consultas (1) ──── (N) recetas
recetas (1) ──── (N) receta_medicamentos
medicamentos (1) ──── (N) receta_medicamentos

medicamentos (1) ──── (N) lotes
establecimientos (1) ──── (N) lotes

pacientes (1) ──── (N) dispensaciones
recetas (1) ──── (N) dispensaciones
medicamentos (1) ──── (N) dispensaciones
lotes (1) ──── (N) dispensaciones
usuarios (1) ──── (N) dispensaciones [farmaceutico_id]

dispensaciones (1) ──── (1) trazabilidad
```

## Diagrama ER

```
┌──────────────────┐     ┌──────────────────┐     ┌──────────────────┐
│ establecimientos  │     │    usuarios       │     │    pacientes     │
├──────────────────┤     ├──────────────────┤     ├──────────────────┤
│ id (PK)          │     │ id (PK)          │     │ id (PK)          │
│ nombre           │     │ username          │     │ ci (UQ)          │
│ tipo             │     │ password          │     │ nombre           │
│ direccion        │     │ nombre            │     │ apellido         │
│ ciudad           │     │ apellido          │     │ fecha_nacimiento │
│ region           │     │ email             │     │ sexo             │
│ telefono         │     │ rol               │     │ direccion        │
│ activo           │     │ establecimiento_id│     │ telefono         │
└──────────────────┘     │ activo            │     │ asegurado        │
                         └──────────────────┘     │ numero_asegurado │
                                                   │ establecimiento_id│
                                                   └──────────────────┘
                                                           │
                                                           │ (1)
                                                           ↓
┌──────────────────┐     ┌──────────────────┐     ┌──────────────────┐
│    recetas        │     │   consultas       │     │   medicamentos   │
├──────────────────┤     ├──────────────────┤     ├──────────────────┤
│ id (PK)          │     │ id (PK)          │     │ id (PK)          │
│ consulta_id (FK) │←───│ paciente_id (FK)  │     │ nombre           │
│ codigo_receta(UQ)│     │ profesional_id(FK)│     │ principio_activo │
│ cobertura_validada│     │ establecimiento_id│     │ forma_farmaceutica│
│ estado           │     │ diagnostico       │     │ concentracion    │
│ fecha_emision    │     │ observaciones     │     │ unidad_medida    │
└───────┬──────────┘     │ fecha_consulta    │     │ codigo_nacional  │
        │                │ estado            │     │ activo           │
        │                └──────────────────┘     └──────────────────┘
        │ (1)                                           │ (1)
        ↓                                                │
┌──────────────────┐     ┌──────────────────┐            │
│receta_medicamentos│    │    lotes         │            │
├──────────────────┤     ├──────────────────┤            │
│ id (PK)          │     │ id (PK)          │            │
│ receta_id (FK)   │     │ medicamento_id(FK)│←──────────┘
│ medicamento_id(FK)│    │ nro_lote          │
│ cantidad_prescrita│    │ fecha_vencimiento │
│ indicaciones     │     │ cantidad          │
└──────────────────┘     │ cantidad_original │
                         │ establecimiento_id │
                         │ precio_unitario   │
                         │ proveedor         │
                         │ estado            │
                         └────────┬─────────┘
                                  │ (1)
                                  │
        ┌─────────────────────────┼─────────────────────────┐
        │                         │                         │
        ↓                         ↓                         ↓
┌──────────────────┐     ┌──────────────────┐     ┌──────────────────┐
│ dispensaciones    │     │  movimientos_stock│    │  trazabilidad    │
├──────────────────┤     ├──────────────────┤     ├──────────────────┤
│ id (PK)          │     │ id (PK)          │     │ id (PK)          │
│ receta_id (FK)   │     │ tipo             │     │ dispensacion_id(FK)│
│ paciente_id (FK) │     │ medicamento_id(FK)│    │ paciente_id (FK)  │
│ medicamento_id(FK)│    │ lote_id (FK)     │     │ medicamento_id(FK)│
│ lote_id (FK)     │     │ cantidad          │     │ lote_id (FK)      │
│ establecimiento_id│     │ establecimiento_id│    │ establecimiento_id│
│ farmaceutico_id   │     │ estab_destino_id │     │ consulta_id (FK)  │
│ cantidad_dispensada│   │ motivo           │     │ receta_id (FK)    │
│ fecha_dispensacion│    │ referencia_id    │     │ observaciones     │
│ observaciones    │     │ fecha_movimiento │     │ fecha_registro    │
│ estado           │     │ usuario_id (FK)  │     └──────────────────┘
└──────────────────┘     └──────────────────┘
```

## Ciclo de Trazabilidad Completo

```
Paciente (CI) → Consulta (SIH) → Receta → Validación Cobertura
     ↓
Farmacia (DASM) → Stock Disponible → Seleccionar Lote/Vencimiento
     ↓
Dispensación → Registro (Paciente-Medicamento-Lote-Fecha)
     ↓
Trazabilidad Completa → Reportes/Auditoría
```