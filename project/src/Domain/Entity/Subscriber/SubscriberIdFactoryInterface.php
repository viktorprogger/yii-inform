<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Subscriber;

interface SubscriberIdFactoryInterface
{
    public function create(?string $id): SubscriberId;
}
