<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Event;

use DateTimeImmutable;

interface EventRepositoryInterface
{
    /**
     * Persists a new event to a DB and sends a corresponding event to the EventDispatcher
     *
     * @param SubscriptionEvent $event
     *
     * @return void
     */
    public function create(SubscriptionEvent $event): void;

    public function exists(EventId $id): bool;

    /**
     * @return SubscriptionEvent[]
     */
    public function read(DateTimeImmutable $since): iterable;
}
