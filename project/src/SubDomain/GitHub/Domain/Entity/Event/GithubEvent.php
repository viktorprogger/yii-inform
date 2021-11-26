<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event;

use DateTimeImmutable;

final class GithubEvent
{
    public function __construct(
        public readonly EventId $id,
        public readonly EventType $type,
        public readonly string $repo,
        public readonly array $payload,
        public readonly DateTimeImmutable $created,
    ) {
    }
}
