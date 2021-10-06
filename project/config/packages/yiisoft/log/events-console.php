<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Yiisoft\Log\Logger;
use Yiisoft\Yii\Debug\Collector\CommandCollector;

return [
    ConsoleTerminateEvent::class => [
        static function (LoggerInterface $logger): void {
            if ($logger instanceof Logger) {
                $logger->flush(true);
            }
        },
        [CommandCollector::class, 'collect']
    ],
];
