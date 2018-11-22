<?php
return [
  'backend' => [
    'frontName' => 'aclogin'
  ],
  'crypt' => [
    'key' => '1c9b9fa872ab8dd43d723335af6169b1'
  ],
  'db' => [
    'table_prefix' => '',
    'connection' => [
      'default' => [
        'host' => '192.168.100.83',
        'dbname' => 'magento2_newdb',
        'username' => 'magento2_newdb',
        'password' => 'd3fault123*',
        'active' => '1'
      ]
    ]
  ],
  'cache' => [
    'frontend' => [
      'default' => [
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => [
          'server' => '127.0.0.1',
          'database' => '0',
          'port' => '6379'
        ]
      ],
      'page_cache' => [
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => [
          'server' => '127.0.0.1',
          'port' => '6380',
          'database' => '0',
          'compress_data' => '0'
        ]
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
  'session' => [
    'save' => 'db'
  ],
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
    'date' => 'Mon, 06 Aug 2018 14:07:39 +0000'
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
