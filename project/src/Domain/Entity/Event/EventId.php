<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Event;

final class EventId
{
    public function __construct(public readonly string $id)
    {
    }
}
