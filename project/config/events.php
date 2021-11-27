<?php

declare(strict_types=1);

use Viktorprogger\YiisoftInform\Infrastructure\SubscriberEventProcessor;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;

return [
    EventCreatedEvent::class => [
        [SubscriberEventProcessor::class, 'handle'],
    ],
];
