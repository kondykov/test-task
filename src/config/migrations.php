<?php
declare(strict_types=1);

return [
    'migrations_paths' => [
        'DoctrineMigrations' => __DIR__ . '/../data/Migrations'
    ],
    'table_storage' => [
        'table_name' => 'migration_versions',
        'version_column_name' => 'version',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at'
    ]
];