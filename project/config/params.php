<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spiral\Database\Driver\MySQL\MySQLDriver;
use Viktorprogger\YiisoftInform\Infrastructure\Queue\RealtimeEventHandler;
use Viktorprogger\YiisoftInform\Infrastructure\Queue\RealtimeEventMessage;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action\HelloAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action\RealtimeAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action\RealtimeEditAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action\SummaryAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action\SummaryEditAction;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Console\LoadEventsCommand;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Console\LoadRepositoriesCommand;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Console\GetUpdatesCommand;
use Yiisoft\Yii\Cycle\Schema\Conveyor\CompositeSchemaConveyor;
use Yiisoft\Yii\Cycle\Schema\Provider\FromConveyorSchemaProvider;
use Yiisoft\Yii\Cycle\Schema\Provider\PhpFileSchemaProvider;

return [
    'telegram routes' => [
        [
            'rule' => static fn (string $data) => $data === '/start',
            'action' => HelloAction::class,
        ],
        [
            'rule' => static fn (string $data) => $data === '/realtime',
            'action' => RealtimeAction::class,
        ],
        [
            'rule' => static fn (string $data) => $data === '/summary',
            'action' => SummaryAction::class,
        ],
        [
            'rule' => static fn (string $data) => preg_match("/^realtime:[+-]:[\w_-]+$/", $data),
            'action' => RealtimeEditAction::class,
        ],
        [
            'rule' => static fn (string $data) => preg_match("/^summary:[+-]:[\w_-]+$/", $data),
            'action' => SummaryEditAction::class,
        ],
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@runtime' => '@root/runtime',
        ],
    ],
    'yiisoft/log-target-file' => [
        'fileTarget' => [
            'file' => '@runtime/logs/app.log',
            'levels' => [
                LogLevel::EMERGENCY,
                LogLevel::ERROR,
                LogLevel::WARNING,
            ],
            'dirMode' => 0755,
            'fileMode' => null,
        ],
        'fileRotator' => [
            'maxFileSize' => 10240,
            'maxFiles' => 5,
            'fileMode' => null,
            'rotateByCopy' => null,
            'compressRotatedFiles' => false,
        ],
    ],
    'yiisoft/router-fastroute' => [
        'enableCache' => true,
    ],
    'yiisoft/yii-console' => [
        'commands' => [
            'inform/tg/updates' => GetUpdatesCommand::class,
            'inform/github/load-repos' => LoadRepositoriesCommand::class,
            'inform/github/load-events' => LoadEventsCommand::class,
        ],
    ],
    'yiisoft/yii-cycle' => [
        // DBAL config
        'dbal' => [
            // SQL query logger. Definition of Psr\Log\LoggerInterface
            'query-logger' => LoggerInterface::class,
            // Default database
            'default' => 'default',
            'aliases' => [],
            'databases' => [
                'default' => ['connection' => 'default']
            ],
            'connections' => [
                'default' => [
                    'driver' => MySQLDriver::class,
                    'connection' => 'mysql:dbname=' . getenv('DB_NAME') . ';host=db',
                    'username' => getenv('DB_LOGIN'),
                    'password' => getenv('DB_PASSWORD'),
                ],
            ],
        ],

        // Cycle migration config
        'migrations' => [
            'directory' => '@root/migrations',
            'namespace' => 'App\\Migration',
            'table' => 'migration',
            'safe' => false,
        ],

        /**
         * Config for {@see \Yiisoft\Yii\Cycle\Factory\OrmFactory}
         * Null, classname or {@see PromiseFactoryInterface} object.
         *
         * @link https://github.com/cycle/docs/blob/master/advanced/promise.md
         */
        'orm-promise-factory' => null,

        /**
         * SchemaProvider list for {@see \Yiisoft\Yii\Cycle\Schema\Provider\Support\SchemaProviderPipeline}
         * Array of classname and {@see SchemaProviderInterface} object.
         * You can configure providers if you pass classname as key and parameters as array:
         * [
         *     SimpleCacheSchemaProvider::class => [
         *         'key' => 'my-custom-cache-key'
         *     ],
         *     FromFilesSchemaProvider::class => [
         *         'files' => ['@runtime/cycle-schema.php']
         *     ],
         *     FromConveyorSchemaProvider::class => [
         *         'generators' => [
         *              Generator\SyncTables::class, // sync table changes to database
         *          ]
         *     ],
         * ]
         */
        'schema-providers' => [
            // Uncomment next line to enable schema cache
            // SimpleCacheSchemaProvider::class => ['key' => 'cycle-orm-cache-key'],
            PhpFileSchemaProvider::class => [
                'file' => '@runtime/schema.php',
                'mode' => PhpFileSchemaProvider::MODE_WRITE_ONLY,
            ],
            FromConveyorSchemaProvider::class => [
                'generators' => [
                    Cycle\Schema\Generator\SyncTables::class, // sync table changes to database
                ],
            ],
        ],

        /**
         * Annotated/attributed entity directories list.
         * {@see \Yiisoft\Aliases\Aliases} are also supported.
         */
        'entity-paths' => ['@root/src'],
        'conveyor' => CompositeSchemaConveyor::class,

        /** @deprecated use `entity-paths` key instead */
        'annotated-entity-paths' => [],
    ],
    'yiisoft/yii-queue' => [
        'handlers' => [
            RealtimeEventMessage::NAME => RealtimeEventHandler::class,
        ],
        'channel-definitions' => [],
    ],
];
