<?php

namespace Yiisoft\Inform\Infrastructure\Entity\Event;

use Cycle\ORM\ORM;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Transaction;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Inform\Domain\Entity\Event\EventCreatedEvent;
use Yiisoft\Inform\Domain\Entity\Event\EventId;
use Yiisoft\Inform\Domain\Entity\Event\EventRepositoryInterface;
use Yiisoft\Inform\Domain\Entity\Event\SubscriptionEvent;

final class EventRepository implements EventRepositoryInterface
{
    private readonly Repository $repository;

    public function __construct(private readonly ORM $orm, private readonly EventDispatcherInterface $eventDispatcher)
    {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->repository = $this->orm->getRepository(SubscriberEventEntity::class);
    }

    public function create(SubscriptionEvent $event): void
    {
        $entity = new SubscriberEventEntity(
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
}