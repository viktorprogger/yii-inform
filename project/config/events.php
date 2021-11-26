<?php

declare(strict_types=1);

use Yiisoft\Inform\Infrastructure\SubscriberEventProcessor;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;

return [
    EventCreatedEvent::class => [
        [SubscriberEventProcessor::class, 'handle'],
    ],
];
