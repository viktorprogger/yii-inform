<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Queue;

use RuntimeException;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Domain\RealtimeSubscription\EventSender;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Yiisoft\Yii\Queue\Message\MessageInterface;

final class RealtimeEventHandler
{
    public function __construct(
        private readonly EventIdFactoryInterface $eventIdFactory,
        private readonly SubscriberIdFactoryInterface $subscriberIdFactory,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly EventSender $eventSender,
    ) {
    }

    public function __invoke(MessageInterface $message): void
    {
        ['event' => $eventId, 'subscriberId' => $subscriberId] = $message->getData();
        $eventId = $this->eventIdFactory->create($eventId);
        $subscriberId = $this->subscriberIdFactory->create($subscriberId);
        $event = $this->eventRepository->find($eventId);
        $subscriber = $this->subscriberRepository->find($subscriberId);

        if ($event === null || $subscriber === null) {
            // TODO throw two specific exceptions
            throw new RuntimeException('Entity not found');
        }

        $this->eventSender->send($event, $subscriber);
    }
}
