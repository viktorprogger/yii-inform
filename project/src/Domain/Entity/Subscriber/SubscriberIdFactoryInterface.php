<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\Entity\Subscriber;

interface SubscriberIdFactoryInterface
{
    public function create(?string $id): SubscriberId;
}
