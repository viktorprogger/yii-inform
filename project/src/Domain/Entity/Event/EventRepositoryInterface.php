<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Event;

use DateTimeImmutable;

interface EventRepositoryInterface
{
    public function create(SubscriptionEvent $event): void;

    /**
     * @return SubscriptionEvent[]
     */
    public function read(DateTimeImmutable $since): iterable;
}
