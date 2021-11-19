<?php

namespace Yiisoft\Inform\Infrastructure\Queue;

use Yiisoft\Inform\Domain\Entity\Event\EventIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Event\EventRepositoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\Domain\RealtimeEventSender;
use Yiisoft\Yii\Queue\Message\MessageInterface;

final class RealtimeEventHandler
{
    public function __construct(
        private readonly EventIdFactoryInterface $eventIdFactory,
        private readonly SubscriberIdFactoryInterface $subscriberIdFactory,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly RealtimeEventSender $eventSender,
    ) {
    }

    public function __invoke(MessageInterface $message)
    {
        ['event' => $eventId, 'subscriberId' => $subscriberId] = $message->getData();
        $eventId = $this->eventIdFactory->create($eventId);
        $subscriberId = $this->subscriberIdFactory->create($subscriberId);
        $event = $this->eventRepository->find($eventId);
    }
}
