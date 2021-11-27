<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\Entity\Subscriber;

final class Settings
{
    public function __construct(
        public readonly array $realtimeRepositories = [],
        public readonly array $summaryRepositories = [],
    ) {
    }
}
