# Diagrama de Flujo — Trazabilidad de Medicamentos (SIH / DASM)

## Flujograma Completo del Sistema

```
                        ┌─────────┐
                        │  INICIO │
                        └────┬────┘
                             │
                             ↓
                  ┌──────────────────────┐
                  │  FASE 1: Identificar  │
                  │  Ingreso N° de CI     │
                  └──────────┬────────────┘
                             │
                             ↓
                     ┌───────────────┐
                     │ ¿Es asegurado?│
                     └───┬───────┬───┘
                         │       │
                    No ←─┘       └──→ Sí
                         │            │
                         ↓            ↓
                ┌─────────────┐ ┌──────────────────────┐
                │Fin/Derivación│ │ FASE 2: Registro de │
                └─────────────┘ │ Consulta (SIH)       │
                                └──────────┬───────────┘
                                           │
                                           ↓
                                ┌──────────────────────┐
                                │ Atención médica       │
                                │ Generación de receta  │
                                └──────────┬───────────┘
                                           │
                                           ↓
                                ┌──────────────────────┐
                                │ FASE 3: Validación   │
                                │ de cobertura/receta  │
                                └──────────┬───────────┘
                                           │
                                     ┌─────┴─────┐
                                     │           │
                                Válida ←──┘  └──→ Rechazada
                                     │           │
                                     ↓           ↓
                          ┌──────────────────┐  ┌─────────────┐
                          │ FASE 4: Farmacia │  │Fin/Revisión  │
                          │ Consulta stock   │  └─────────────┘
                          │  (DASM)          │
                          └────────┬─────────┘
                                   │
                                   ↓
                          ┌───────────────┐
                          │¿Stock disponible│
                          └───┬───────┬───┘
                              │       │
                         No ←─┘       └──→ Sí
                              │            │
                              ↓            ↓
                    ┌────────────────┐ ┌───────────────────┐
                    │Alternativa/    │ │ Seleccionar lote  │
                    │Reposición     │ │ y vencimiento     │
                    └────────────────┘ └──────────┬────────┘
                                                  │
                                                  ↓
                                    ┌──────────────────────┐
                                    │ FASE 5: Dispensación │
                                    │ Registro completo    │
                                    │ - Paciente           │
                                    │ - Medicamento        │
                                    │ - Lote               │
                                    │ - Fecha vencimiento  │
                                    │ - Fecha dispensación │
                                    └──────────┬───────────┘
                                               │
                                               ↓
                                    ┌──────────────────────┐
                                    │ Actualizar stock     │
                                    │ (decrementar)        │
                                    └──────────┬───────────┘
                                               │
                                               ↓
                                    ┌──────────────────────┐
                                    │ FASE 6: Trazabilidad │
                                    │ Consolidación        │
                                    │ Información completa  │
                                    └──────────┬───────────┘
                                               │
                                               ↓
                                        ┌─────────┐
                                        │   FIN   │
                                        └─────────┘
```

## Mapeo Fase → Módulo del Sistema

| Fase | Módulo | Controller | Acción |
|------|--------|-----------|--------|
| 1 | Paciente | PacienteController | buscar, buscarAjax |
| 2 | Consulta | ConsultaController | crear, guardar |
| 3 | Receta | RecetaController | crear, validar, validarCobertura |
| 4 | Farmacia | FarmaciaController | stock, lotes |
| 5 | Dispensación | DispensacionController | procesar, guardar |
| 6 | Trazabilidad | TrazabilidadController | seguimiento, detalle |