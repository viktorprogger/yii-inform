<?php

declare(strict_types=1);

use Sentry\State\HubInterface;
use Sentry\Tracing\TransactionContext;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Viktorprogger\YiisoftInform\Infrastructure\RequestId;
use Viktorprogger\YiisoftInform\Infrastructure\SubscriberEventProcessor;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;
use Yiisoft\Yii\Http\Event\AfterEmit;
use Yiisoft\Yii\Http\Event\BeforeRequest;
use Yiisoft\Yii\Sentry\SentryConsoleHandler;

return [
    EventCreatedEvent::class => [
        [SubscriberEventProcessor::class, 'handle'],
    ],
    ConsoleErrorEvent::class => [
        [SentryConsoleHandler::class, 'handle'],
    ],
    BeforeRequest::class => [
        static fn(HubInterface $hub) => $hub->startTransaction(new TransactionContext()),
        static fn(RequestId $requestId) => $requestId->regenerate(),
    ],
    AfterEmit::class => [
        static fn(HubInterface $hub) => $hub->getTransaction()?->finish(),
    ],
];
