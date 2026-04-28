CREATE DATABASE IF NOT EXISTS ips_trazabilidad
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ips_trazabilidad;

CREATE TABLE establecimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo ENUM('centro_distribucion','farmacia_periferica','hospital') NOT NULL,
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    region VARCHAR(100),
    telefono VARCHAR(30),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_ciudad (ciudad),
    INDEX idx_region (region)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    principio_activo VARCHAR(200) NOT NULL,
    forma_farmaceutica ENUM('comprimido','capsula','inyectable','jarabe','crema','gotas','otro') NOT NULL,
    concentracion VARCHAR(100),
    unidad_medida VARCHAR(50),
    codigo_nacional VARCHAR(50) UNIQUE,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_principio_activo (principio_activo),
    INDEX idx_forma (forma_farmaceutica),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('administrador','profesional_salud','farmaceutico','auditor') NOT NULL,
    establecimiento_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_rol (rol),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ci VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    sexo ENUM('M','F','O') NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(30),
    asegurado TINYINT(1) DEFAULT 1,
    numero_asegurado VARCHAR(50),
    establecimiento_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pacientes_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_ci (ci),
    INDEX idx_nombre_apellido (nombre, apellido),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_asegurado (asegurado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    profesional_id INT NOT NULL,
    establecimiento_id INT NOT NULL,
    diagnostico TEXT,
    observaciones TEXT,
    fecha_consulta DATETIME NOT NULL,
    estado ENUM('activa','cerrada','cancelada') DEFAULT 'activa',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_consultas_paciente
        FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_consultas_profesional
        FOREIGN KEY (profesional_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_consultas_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_profesional (profesional_id),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_fecha_consulta (fecha_consulta),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consulta_id INT NOT NULL,
    codigo_receta VARCHAR(30) NOT NULL UNIQUE,
    cobertura_validada TINYINT(1) DEFAULT 0,
    estado ENUM('pendiente','validada','rechazada','dispensada','cancelada') DEFAULT 'pendiente',
    fecha_emision DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_recetas_consulta
        FOREIGN KEY (consulta_id) REFERENCES consultas(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_consulta (consulta_id),
    INDEX idx_codigo_receta (codigo_receta),
    INDEX idx_estado (estado),
    INDEX idx_fecha_emision (fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE receta_medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receta_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    cantidad_prescrita INT NOT NULL,
    indicaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_receta_medicamentos_receta
        FOREIGN KEY (receta_id) REFERENCES recetas(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_receta_medicamentos_medicamento
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_receta (receta_id),
    INDEX idx_medicamento (medicamento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicamento_id INT NOT NULL,
    nro_lote VARCHAR(50) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    cantidad INT NOT NULL,
    cantidad_original INT NOT NULL,
    establecimiento_id INT NOT NULL,
    precio_unitario DECIMAL(10,2),
    proveedor VARCHAR(150),
    estado ENUM('disponible','agotado','vencido','retirado') DEFAULT 'disponible',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lotes_medicamento
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_lotes_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE INDEX idx_lote_medicamento_estab (nro_lote, medicamento_id, establecimiento_id),
    INDEX idx_medicamento (medicamento_id),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_fecha_vencimiento (fecha_vencimiento),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dispensaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receta_id INT NOT NULL,
    paciente_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    lote_id INT NOT NULL,
    establecimiento_id INT NOT NULL,
    farmaceutico_id INT NOT NULL,
    cantidad_dispensada INT NOT NULL,
    fecha_dispensacion DATETIME NOT NULL,
    observaciones TEXT,
    estado ENUM('completada','parcial','cancelada') DEFAULT 'completada',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_dispensaciones_receta
        FOREIGN KEY (receta_id) REFERENCES recetas(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_dispensaciones_paciente
        FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_dispensaciones_medicamento
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_dispensaciones_lote
        FOREIGN KEY (lote_id) REFERENCES lotes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_dispensaciones_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_dispensaciones_farmaceutico
        FOREIGN KEY (farmaceutico_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_receta (receta_id),
    INDEX idx_paciente (paciente_id),
    INDEX idx_medicamento (medicamento_id),
    INDEX idx_lote (lote_id),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_farmaceutico (farmaceutico_id),
    INDEX idx_fecha_dispensacion (fecha_dispensacion),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('entrada','salida','transferencia') NOT NULL,
    medicamento_id INT NOT NULL,
    lote_id INT NOT NULL,
    cantidad INT NOT NULL,
    establecimiento_origen_id INT NOT NULL,
    establecimiento_destino_id INT NULL,
    motivo VARCHAR(255),
    referencia_id INT NULL,
    fecha_movimiento DATETIME NOT NULL,
    usuario_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_movimientos_medicamento
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_lote
        FOREIGN KEY (lote_id) REFERENCES lotes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_origen
        FOREIGN KEY (establecimiento_origen_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_destino
        FOREIGN KEY (establecimiento_destino_id) REFERENCES establecimientos(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_medicamento (medicamento_id),
    INDEX idx_lote (lote_id),
    INDEX idx_origen (establecimiento_origen_id),
    INDEX idx_destino (establecimiento_destino_id),
    INDEX idx_fecha_movimiento (fecha_movimiento),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trazabilidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispensacion_id INT NOT NULL,
    paciente_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    lote_id INT NOT NULL,
    establecimiento_id INT NOT NULL,
    consulta_id INT NOT NULL,
    receta_id INT NOT NULL,
    fecha_registro DATETIME NOT NULL,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trazabilidad_dispensacion
        FOREIGN KEY (dispensacion_id) REFERENCES dispensaciones(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_trazabilidad_paciente
        FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trazabilidad_medicamento
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trazabilidad_lote
        FOREIGN KEY (lote_id) REFERENCES lotes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trazabilidad_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trazabilidad_consulta
        FOREIGN KEY (consulta_id) REFERENCES consultas(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trazabilidad_receta
        FOREIGN KEY (receta_id) REFERENCES recetas(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_dispensacion (dispensacion_id),
    INDEX idx_paciente (paciente_id),
    INDEX idx_medicamento (medicamento_id),
    INDEX idx_lote (lote_id),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_consulta (consulta_id),
    INDEX idx_receta (receta_id),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('vencimiento_proximo','stock_bajo','desvio','error_dispensacion') NOT NULL,
    establecimiento_id INT NOT NULL,
    medicamento_id INT NULL,
    lote_id INT NULL,
    mensaje TEXT NOT NULL,
    prioridad ENUM('alta','media','baja') DEFAULT 'media',
    estado ENUM('activa','atendida','descartada') DEFAULT 'activa',
    fecha_alerta DATETIME NOT NULL,
    atendida_por INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_alertas_establecimiento
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_alertas_medicamento
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_alertas_lote
        FOREIGN KEY (lote_id) REFERENCES lotes(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_alertas_atendida_por
        FOREIGN KEY (atendida_por) REFERENCES usuarios(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_medicamento (medicamento_id),
    INDEX idx_lote (lote_id),
    INDEX idx_prioridad (prioridad),
    INDEX idx_estado (estado),
    INDEX idx_fecha_alerta (fecha_alerta),
    INDEX idx_atendida_por (atendida_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;