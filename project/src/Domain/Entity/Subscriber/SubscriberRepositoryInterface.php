<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Subscriber;

interface SubscriberRepositoryInterface
{
    public function create(Subscriber $subscriber): void;

    public function find(SubscriberId $id): ?Subscriber;

    public function updateSettings(SubscriberId $id, Settings $settings): void;
}
