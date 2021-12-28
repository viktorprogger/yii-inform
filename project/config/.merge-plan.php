<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'common' => [
            'yiisoft/cache' => [
                'config/common.php',
            ],
            'yiisoft/request-model' => [
                'config/common.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/common.php',
            ],
            'yiisoft/yii-cycle' => [
                'config/common.php',
            ],
            'yiisoft/yii-queue-amqp' => [
                'config/common.php',
            ],
            'yiisoft/yii-sentry' => [
                'config/common.php',
            ],
            'yiisoft/aliases' => [
                'config/common.php',
            ],
            'yiisoft/log-target-file' => [
                'config/common.php',
            ],
            'yiisoft/validator' => [
                'config/common.php',
            ],
            'yiisoft/router' => [
                'config/common.php',
            ],
            'yiisoft/yii-event' => [
                'config/common.php',
            ],
            'yiisoft/yii-queue' => [
                'config/common.php',
            ],
            '/' => [
                'common.php',
            ],
        ],
        'params' => [
            'yiisoft/data-response' => [
                'config/params.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/params.php',
            ],
            'yiisoft/yii-cycle' => [
                'config/params.php',
            ],
            'yiisoft/yii-sentry' => [
                'config/params.php',
            ],
            'yiisoft/aliases' => [
                'config/params.php',
            ],
            'yiisoft/log-target-file' => [
                'config/params.php',
            ],
            'yiisoft/yii-console' => [
                'config/params.php',
            ],
            'yiisoft/yii-queue' => [
                'config/params.php',
            ],
            '/' => [
                'params.php',
            ],
        ],
        'web' => [
            'yiisoft/data-response' => [
                'config/web.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/web.php',
            ],
            'yiisoft/error-handler' => [
                'config/web.php',
            ],
            'yiisoft/middleware-dispatcher' => [
                'config/web.php',
            ],
            'yiisoft/yii-event' => [
                'config/web.php',
            ],
            '/' => [
                '$common',
                'web.php',
            ],
        ],
        'console' => [
            'yiisoft/yii-cycle' => [
                'config/console.php',
            ],
            'yiisoft/yii-console' => [
                'config/console.php',
            ],
            'yiisoft/yii-event' => [
                'config/console.php',
            ],
            '/' => [
                '$common',
                'console.php',
            ],
        ],
        'events-console' => [
            'yiisoft/yii-cycle' => [
                'config/events-console.php',
            ],
            'yiisoft/yii-sentry' => [
                'config/events-console.php',
            ],
            'yiisoft/log' => [
                'config/events-console.php',
            ],
            'yiisoft/yii-console' => [
                'config/events-console.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'config/events-console.php',
            ],
        ],
        'delegates' => [
            'yiisoft/yii-cycle' => [
                'config/delegates.php',
            ],
        ],
        'bootstrap' => [
            'yiisoft/yii-sentry' => [
                'config/bootstrap.php',
            ],
        ],
        'events-web' => [
            'yiisoft/log' => [
                'config/events-web.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'config/events-web.php',
            ],
        ],
        'providers-console' => [
            'yiisoft/yii-console' => [
                'config/providers-console.php',
            ],
        ],
        'events' => [
            'yiisoft/yii-event' => [
                'config/events.php',
            ],
            '/' => [
                'events.php',
            ],
        ],
        'routes' => [
            '/' => [
                'routes.php',
            ],
        ],
        'delegates-console' => [
            '/' => [
                '$delegates',
            ],
        ],
        'delegates-web' => [
            '/' => [
                '$delegates',
            ],
        ],
        'providers-web' => [
            '/' => [
                'dummy.php',
            ],
        ],
        'bootstrap-console' => [
            '/' => [
                'dummy.php',
            ],
        ],
        'bootstrap-web' => [
            '/' => [
                'dummy.php',
            ],
        ],
    ],
    'dev' => [
        'params' => [
            '/' => [
                'params-dev.php',
            ],
        ],
    ],
    'prod' => [
        'params' => [
            '/' => [
                'params-prod.php',
            ],
        ],
    ],
];
