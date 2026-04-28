<?php declare(strict_types=1);

return [
    'admin' => [
        'permissions' => ['dashboard', 'medications', 'batches', 'traceability', 'reports', 'users', 'settings'],
    ],
    'regulator' => [
        'permissions' => ['dashboard', 'medications', 'batches', 'traceability', 'reports'],
    ],
    'laboratory' => [
        'permissions' => ['dashboard', 'medications', 'batches', 'traceability'],
    ],
    'distributor' => [
        'permissions' => ['dashboard', 'traceability', 'reports'],
    ],
    'pharmacy' => [
        'permissions' => ['dashboard', 'traceability'],
    ],
];