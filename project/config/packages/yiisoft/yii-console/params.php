<?php

declare(strict_types=1);

use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Console\GetUpdatesCommand;
use Yiisoft\Yii\Console\Command\Serve;
use Yiisoft\Yii\Cycle\Command\Schema;
use Yiisoft\Yii\Cycle\Command\Migration;
use Yiisoft\Yii\Debug\Command\ResetCommand;

return [
    'yiisoft/yii-console' => [
        'id' => 'yii-console',
        'name' => 'Yii Console',
        'autoExit' => false,
        'commands' => [
            'serve' => Serve::class,

            'cycle/schema' => Schema\SchemaCommand::class,
            'cycle/schema/php' => Schema\SchemaPhpCommand::class,
            'cycle/schema/clear' => Schema\SchemaClearCommand::class,
            'cycle/schema/rebuild' => Schema\SchemaRebuildCommand::class,
            'migrate/create' => Migration\CreateCommand::class,
            'migrate/generate' => Migration\GenerateCommand::class,
            'migrate/up' => Migration\UpCommand::class,
            'migrate/down' => Migration\DownCommand::class,
            'migrate/list' => Migration\ListCommand::class,

            'debug/reset' => ResetCommand::class,

            'inform/updates' => GetUpdatesCommand::class,
        ],
        'version' => '3.0',
    ],
];
