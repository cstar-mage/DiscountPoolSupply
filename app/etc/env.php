<?php
return [
    'backend' => [
        'frontName' => 'admin123'
    ],
    'crypt' => [
        'key' => 'zajtwwndgepusoycvnggt5eeqgvrlhj6'
    ],
    'session' => [
        'save' => 'db'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'dpsmagento',
                'username' => 'dpoolsup',
                'password' => 'JLQ5rPctt5rNV6bN',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'cache_types' => [
        'config' => 0,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 0,
        'reflection' => 0,
        'db_ddl' => 0,
        'eav' => 0,
        'customer_notification' => 0,
        'config_integration' => 0,
        'config_integration_api' => 0,
        'full_page' => 0,
        'translate' => 0,
        'config_webservice' => 0,
        'compiled_config' => 1
    ],
    'install' => [
        'date' => 'Wed, 17 Jan 2018 16:09:43 -0500'
    ],
    'system' => [
        'default' => [
            'dev' => [
                'debug' => [
                    'debug_logging' => '0'
                ]
            ]
        ]
    ]
];
