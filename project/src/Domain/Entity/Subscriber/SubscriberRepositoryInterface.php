<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\Entity\Subscriber;

interface SubscriberRepositoryInterface
{
    public function create(Subscriber $subscriber): void;

    public function find(SubscriberId $id): ?Subscriber;

    /**
     * @param string $repo
     *
     * @return SubscriberId[]
     */
    public function findForRealtimeRepo(string $repo): array;

    /**
     * Find subscribers who didn't recieve
     *
     * @return SubscriberId[]
     */
    public function findForSummary(): array;

    public function getAllIds(): array;

    public function updateSettings(Subscriber $subscriber, Settings $settings): void;
}
