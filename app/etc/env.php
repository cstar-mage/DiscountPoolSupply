<?php
return [
    'backend' => [
        'frontName' => 'admin123'
    ],
    'crypt' => [
        'key' => 'zajtwwndgepusoycvnggt5eeqgvrlhj6'
    ],
    'session' => [
//        'save' => 'db'
        'save' => 'files',
        'save_path' => 'var/session'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
//                'host' => 'discountnew.cepl6na8nshq.ca-central-1.rds.amazonaws.com',
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
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'translate' => 1,
        'config_webservice' => 1,
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
