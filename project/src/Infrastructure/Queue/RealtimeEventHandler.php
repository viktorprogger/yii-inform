<?php

namespace Yiisoft\Inform\Infrastructure\Queue;

use RuntimeException;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\Domain\RealtimeSubscription\EventSender;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
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

    public function __invoke(MessageInterface $message)
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
