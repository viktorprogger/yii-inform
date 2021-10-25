<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'common' => [
            '/' => [
                'config/common.php',
            ],
            'yiisoft/cache-file' => [
                'common.php',
            ],
            'yiisoft/log-target-file' => [
                'common.php',
            ],
            'yiisoft/request-model' => [
                'common.php',
            ],
            'yiisoft/router-fastroute' => [
                'common.php',
            ],
            'yiisoft/yii-cycle' => [
                'common.php',
            ],
            'yiisoft/yii-debug' => [
                'common.php',
            ],
            'yiisoft/validator' => [
                'common.php',
            ],
            'yiisoft/router' => [
                'common.php',
            ],
            'yiisoft/yii-event' => [
                'common.php',
            ],
            'yiisoft/profiler' => [
                'common.php',
            ],
            'yiisoft/view' => [
                'common.php',
            ],
            'yiisoft/yii-filesystem' => [
                'common.php',
            ],
            'yiisoft/aliases' => [
                'common.php',
            ],
            'yiisoft/cache' => [
                'common.php',
            ],
        ],
        'console' => [
            '/' => [
                '$common',
                'config/console/*.php',
            ],
            'yiisoft/yii-cycle' => [
                'console.php',
            ],
            'yiisoft/yii-debug' => [
                'console.php',
            ],
            'yiisoft/yii-console' => [
                'console.php',
            ],
            'yiisoft/yii-event' => [
                'console.php',
            ],
        ],
        'delegates' => [
            'yiisoft/yii-cycle' => [
                'delegates.php',
            ],
        ],
        'events' => [
            'yiisoft/yii-event' => [
                'events.php',
            ],
        ],
        'events-console' => [
            'yiisoft/yii-cycle' => [
                'events-console.php',
            ],
            'yiisoft/yii-debug' => [
                'events-console.php',
            ],
            'yiisoft/log' => [
                'events-console.php',
            ],
            'yiisoft/yii-console' => [
                'events-console.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'events-console.php',
            ],
        ],
        'events-web' => [
            'yiisoft/yii-debug' => [
                'events-web.php',
            ],
            'yiisoft/log' => [
                'events-web.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'events-web.php',
            ],
            'yiisoft/profiler' => [
                'events-web.php',
            ],
        ],
        'params' => [
            'yiisoft/cache-file' => [
                'params.php',
            ],
            'yiisoft/data-response' => [
                'params.php',
            ],
            'yiisoft/log-target-file' => [
                'params.php',
            ],
            'yiisoft/router-fastroute' => [
                'params.php',
            ],
            'yiisoft/yii-cycle' => [
                'params.php',
            ],
            'yiisoft/yii-debug' => [
                'params.php',
            ],
            'yiisoft/yii-console' => [
                'params.php',
            ],
            'yiisoft/assets' => [
                'params.php',
            ],
            'yiisoft/profiler' => [
                'params.php',
            ],
            'yiisoft/view' => [
                'params.php',
            ],
            'yiisoft/aliases' => [
                'params.php',
            ],
        ],
        'providers' => [
            'yiisoft/yii-debug' => [
                'providers.php',
            ],
        ],
        'providers-console' => [
            'yiisoft/yii-console' => [
                'providers-console.php',
            ],
        ],
        'routes' => [
            '/' => [
                'config/routes.php',
            ],
        ],
        'tests' => [
            'yiisoft/yii-debug' => [
                'tests.php',
            ],
        ],
        'web' => [
            '/' => [
                '$common',
                'config/web/*.php',
            ],
            'yiisoft/data-response' => [
                'web.php',
            ],
            'yiisoft/error-handler' => [
                'web.php',
            ],
            'yiisoft/router-fastroute' => [
                'web.php',
            ],
            'yiisoft/yii-debug' => [
                'web.php',
            ],
            'yiisoft/middleware-dispatcher' => [
                'web.php',
            ],
            'yiisoft/yii-event' => [
                'web.php',
            ],
            'yiisoft/assets' => [
                'web.php',
            ],
            'yiisoft/view' => [
                'web.php',
            ],
        ],
    ],
];
