<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event;

final class EventId
{
    public function __construct(public readonly string $id)
    {
    }
}
