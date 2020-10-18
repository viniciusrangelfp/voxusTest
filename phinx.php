<?php

include_once __DIR__.'/core/boostrap.php';

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migration'
    ],
    'environments' => [
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'sqlite',
            'name' => 'database/sqlite/'.getenv('DATABASE_NAME')
        ]
    ],
    'version_order' => 'creation'
];
