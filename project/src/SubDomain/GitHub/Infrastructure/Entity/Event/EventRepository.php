<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\Event;

use Cycle\ORM\ORM;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Transaction;
use DateTimeImmutable;
use JsonException;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventId;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;

final class EventRepository implements EventRepositoryInterface
{
    private readonly Repository $repository;

    public function __construct(
        private readonly ORM $orm,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EventIdFactoryInterface $idFactory,
    ) {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->repository = $this->orm->getRepository(EventEntity::class);
    }

    public function create(GithubEvent $event): void
    {
        $entity = new EventEntity(
            $event->id->value,
            $event->type->value,
            $event->repo,
            json_encode($event->payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), // TODO remove pretty print
            $event->created,
        );
        (new Transaction($this->orm))->persist($entity)->run();
        $this->eventDispatcher->dispatch(new EventCreatedEvent($event));
    }

    public function enrich(GithubEvent $event, mixed $payload): GithubEvent
    {
        /** @var EventEntity|null $entity */
        $entity = $this->repository->findByPK($event->id->value);
        if ($entity === null) {
            throw new RuntimeException('Event not found'); // TODO a beautiful exception
        }

        $entity->payload = json_encode(array_merge(json_decode($entity->payload, true, flags: JSON_THROW_ON_ERROR), $payload));
        (new Transaction($this->orm))->persist($entity)->run();

        return $this->factory($entity);
    }

    public function exists(EventId $id): bool
    {
        return $this->repository->select()->wherePK($id->value)->count('id') > 0;
    }

    public function find(EventId $id): ?GithubEvent
    {
        /** @var EventEntity|null $entity */
        $entity = $this->repository->findByPK($id->value);
        if ($entity === null) {
            return null;
        }

        return $this->factory($entity);
    }

    /**
     * @param EventEntity $entity
     *
     * @return GithubEvent
     * @throws JsonException
     */
    private function factory(EventEntity $entity): GithubEvent
    {
        return new GithubEvent(
            $this->idFactory->create($entity->id),
            EventType::from($entity->type),
            $entity->repo,
            json_decode($entity->payload, true, flags: JSON_THROW_ON_ERROR),
            $entity->created,
        );
    }
}
