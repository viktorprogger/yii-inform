<?php

declare(strict_types=1);

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Viktorprogger\YiisoftInform\Infrastructure\SubscriberEventProcessor;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;
use Yiisoft\Yii\Sentry\SentryConsoleHandler;

return [
    EventCreatedEvent::class => [
        [SubscriberEventProcessor::class, 'handle'],
    ],
    ConsoleErrorEvent::class => [
        [SentryConsoleHandler::class, 'handle'],
    ],
];
