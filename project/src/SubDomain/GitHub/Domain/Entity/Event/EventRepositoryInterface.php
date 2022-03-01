<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event;

interface EventRepositoryInterface
{
    /**
     * Persists a new event to a DB and sends a corresponding event to the EventDispatcher
     *
     * @param GithubEvent $event
     *
     * @return void
     */
    public function create(GithubEvent $event): void;

    public function enrich(GithubEvent $event, mixed $payload): GithubEvent;

    public function exists(EventId $id): bool;

    public function find(EventId $id): ?GithubEvent;
}
