<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event;

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

    public function withPayload(array $payload): self
    {
        return new self(
            $this->id,
            $this->type,
            $this->repo,
            $payload,
            $this->created,
        );
    }
}
