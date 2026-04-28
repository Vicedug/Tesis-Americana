<?php

declare(strict_types=1);

defined('APP_NAME')          || define('APP_NAME', 'Trazabilidad IPS');
defined('APP_VERSION')       || define('APP_VERSION', '1.0.0');
defined('UPLOAD_PATH')       || define('UPLOAD_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads');
defined('MAX_UPLOAD_SIZE')   || define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
defined('SESSION_LIFETIME')  || define('SESSION_LIFETIME', 1800);
defined('ITEMS_PER_PAGE')    || define('ITEMS_PER_PAGE', 15);
defined('DATE_FORMAT')       || define('DATE_FORMAT', 'd/m/Y');
defined('DATETIME_FORMAT')   || define('DATETIME_FORMAT', 'd/m/Y H:i');

defined('ROLES') || define('ROLES', [
    'administrador',
    'profesional_salud',
    'farmaceutico',
    'auditor',
]);

defined('ESTADO_RECETA') || define('ESTADO_RECETA', [
    'pendiente',
    'validada',
    'rechazada',
    'dispensada',
    'cancelada',
]);

defined('ESTADO_LOTE') || define('ESTADO_LOTE', [
    'disponible',
    'agotado',
    'vencido',
    'retirado',
]);

defined('ESTADO_DISPENSACION') || define('ESTADO_DISPENSACION', [
    'completada',
    'parcial',
    'cancelada',
]);

defined('TIPO_MOVIMIENTO') || define('TIPO_MOVIMIENTO', [
    'entrada',
    'salida',
    'transferencia',
]);

defined('ALERTA_VENCIMIENTO_DIAS') || define('ALERTA_VENCIMIENTO_DIAS', 90);
defined('STOCK_MINIMO')            || define('STOCK_MINIMO', 10);
