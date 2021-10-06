<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Event;

use DateTimeImmutable;

final class SubscriptionEvent
{
    public function __construct(
        public readonly EventId $id,
        public readonly EventType $type,
        public readonly string $repo,
        public readonly DateTimeImmutable $created,
    ) {
    }
}
