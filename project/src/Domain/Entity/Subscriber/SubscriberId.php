<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\Entity\Subscriber;

final class SubscriberId
{
    public function __construct(public readonly string $value)
    {
    }
}
