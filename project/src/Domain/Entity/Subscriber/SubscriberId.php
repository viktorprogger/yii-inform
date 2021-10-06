<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Subscriber;

final class SubscriberId
{
    public function __construct(public readonly string $value)
    {
    }
}
