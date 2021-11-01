<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Subscriber;

final class Subscriber
{
    public function __construct(public readonly SubscriberId $id, public readonly string $chatId, public readonly Settings $settings)
    {
    }
}
