<?php

namespace Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Entity\Event;

use Cycle\ORM\ORM;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Transaction;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventId;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;

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
            $event->id->id,
            $event->type->value,
            $event->repo,
            json_encode($event->payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), // TODO remove pretty print
            $event->created,
        );
        (new Transaction($this->orm))->persist($entity)->run();
        $this->eventDispatcher->dispatch(new EventCreatedEvent($event));
    }

    public function exists(EventId $id): bool
    {
        return $this->repository->select()->wherePK($id->id)->count('id') > 0;
    }

    public function read(DateTimeImmutable $since): iterable
    {
        // TODO: Implement read() method.
    }

    public function find(EventId $id): ?GithubEvent
    {
        /** @var EventEntity|null $entity */
        $entity = $this->repository->findByPK($id->id);
        if ($entity === null) {
            return null;
        }

        return new GithubEvent(
            $this->idFactory->create($entity->id),
            EventType::from($entity->type),
            $entity->repo,
            json_decode($entity->payload, true, flags: JSON_THROW_ON_ERROR),
            $entity->created,
        );
    }
}
