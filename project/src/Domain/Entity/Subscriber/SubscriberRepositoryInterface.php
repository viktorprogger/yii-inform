<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Subscriber;

interface SubscriberRepositoryInterface
{
    public function create(Subscriber $subscriber): void;

    public function find(SubscriberId $id): ?Subscriber;

    /**
     * @param string $repo
     *
     * @return SubscriberId[]
     */
    public function findForRealtimeRepo(string $repo): iterable;

    public function updateSettings(Subscriber $subscriber, Settings $settings): void;
}
