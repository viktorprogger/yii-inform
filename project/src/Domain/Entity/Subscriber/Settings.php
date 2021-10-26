<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Subscriber;

final class Settings
{
    public function __construct(public readonly array $realtimeRepositories = [])
    {
    }
}
