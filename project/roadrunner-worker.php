<?php

declare(strict_types=1);

use Yiisoft\Config\Config;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Config\Modifier\RecursiveMerge;
use Yiisoft\Config\Modifier\RemoveFromVendor;
use Yiisoft\Config\Modifier\ReverseMerge;
use Yiisoft\Yii\Runner\RoadRunner\RoadRunnerApplicationRunner;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/preload.php';

$environment = null;

(new RoadRunnerApplicationRunner(__DIR__, true, $environment))
    ->withConfig(
        new Config(
            new ConfigPaths(__DIR__, 'config'),
            $environment,
            [
                ReverseMerge::groups('events', 'events-web', 'events-console'),
                RecursiveMerge::groups('params', 'events', 'events-web', 'events-console'),
                RemoveFromVendor::keys(['yiisoft/log-target-file', 'fileTarget', 'levels']),
            ],
        )
    )
    ->run();
