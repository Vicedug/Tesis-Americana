-- ============================================================
-- Seed: Medication Traceability System - IPS Paraguay
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- establecimientos
-- -----------------------------------------------------------
INSERT INTO establecimientos (id, nombre, tipo, ciudad, region, activo) VALUES
  (1, 'Centro de Distribución Nacional IPS', 'centro_distribucion', 'Asunción', 'Central', 1),
  (2, 'Farmacia Hospital de Clínicas',       'hospital',            'Asunción', 'Central', 1),
  (3, 'Farmacia Periférica San Lorenzo',      'farmacia_periferica', 'San Lorenzo', 'Central', 1),
  (4, 'Farmacia Periférica Luque',            'farmacia_periferica', 'Luque', 'Central', 1),
  (5, 'Farmacia Periférica Fernando de la Mora','farmacia_periferica','Fernando de la Mora','Central', 1);

-- -----------------------------------------------------------
-- usuarios
-- password: Admin123!  →  bcrypt hash (cost 10)
-- -----------------------------------------------------------
INSERT INTO usuarios (id, usuario, password_hash, rol, establecimiento_id, activo) VALUES
  (1, 'admin',         '$2y$10$EIX2b3z5V6W1q8r9N2m4KO5e6vHgxJqLzMa3fSYEMJ.tbIO7dK7m6', 'administrador',      1, 1),
  (2, 'dra.martinez',  '$2y$10$EIX2b3z5V6W1q8r9N2m4KO5e6vHgxJqLzMa3fSYEMJ.tbIO7dK7m6', 'profesional_salud',  2, 1),
  (3, 'farm.garcia',   '$2y$10$EIX2b3z5V6W1q8r9N2m4KO5e6vHgxJqLzMa3fSYEMJ.tbIO7dK7m6', 'farmaceutico',       2, 1),
  (4, 'auditor.lopez', '$2y$10$EIX2b3z5V6W1q8r9N2m4KO5e6vHgxJqLzMa3fSYEMJ.tbIO7dK7m6', 'auditor',            1, 1);

-- -----------------------------------------------------------
-- medicamentos
-- -----------------------------------------------------------
INSERT INTO medicamentos (id, nombre, concentracion, forma_farmaceutica, activo) VALUES
  (1,  'Amoxicilina',    '500mg', 'comprimido',  1),
  (2,  'Paracetamol',    '500mg', 'comprimido',  1),
  (3,  'Omeprazol',      '20mg',  'cápsula',     1),
  (4,  'Metformina',     '850mg', 'comprimido',  1),
  (5,  'Losartán',       '50mg',  'comprimido',  1),
  (6,  'Ibuprofeno',     '400mg', 'comprimido',  1),
  (7,  'Azitromicina',   '500mg', 'comprimido',  1),
  (8,  'Salbutamol',     '100mcg','inhalador',   1),
  (9,  'Diclofenaco',    '50mg',  'comprimido',  1),
  (10, 'Ciprofloxacino', '500mg', 'comprimido',  1);

-- -----------------------------------------------------------
-- alertas_config
-- -----------------------------------------------------------
INSERT INTO alertas_config (id, parametro, valor, descripcion) VALUES
  (1, 'stock_minimo_umbral',   '10', 'Umbral mínimo de unidades en stock para generar alerta'),
  (2, 'dias_alerta_vencimiento','90', 'Días de antelación para alertar sobre vencimiento de lote'),
  (3, 'notificaciones_activas', '1',  'Activación global del sistema de notificaciones (1=activo, 0=inactivo)');

SET FOREIGN_KEY_CHECKS = 1;