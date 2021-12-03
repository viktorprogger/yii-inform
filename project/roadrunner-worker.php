<?php

declare(strict_types=1);

use Yiisoft\Yii\Runner\RoadRunner\RoadRunnerApplicationRunner;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/preload.php';

(new RoadRunnerApplicationRunner(__DIR__, true, null))->run();
