<?php

namespace Yiisoft\Inform\Infrastructure;

use Yiisoft\Inform\Domain\Entity\Event\EventCreatedEvent;
use Yiisoft\Inform\Domain\Entity\Event\SubscriptionEvent;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\Domain\TelegramMessageGenerator;
use Yiisoft\Inform\Infrastructure\Queue\RealtimeEventMessage;
use Yiisoft\Yii\Queue\Queue;

final class SubscriberEventProcessor
{
    private array $repositories = [];
    private array $subscribers = [];

    public function __construct(
        private readonly TelegramMessageGenerator $messageGenerator,
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly Queue $queue,
    ) {
    }

    public function handle(EventCreatedEvent $event): void
    {
        $this->sendRealtimeSubscribers($event->subscriptionEvent);
    }

    public function sendRealtimeSubscribers(SubscriptionEvent $subscriptionEvent): void
    {
        try {
            foreach ($this->getSubscribers($subscriptionEvent->repo) as $subscriber) {
                $this->queue->push(new RealtimeEventMessage($subscriptionEvent->id->id, $subscriber->id->value));
            }
        } finally {
            $this->repositories = [];
            $this->subscribers = [];
        }
    }

    private function getSubscribers(string $repo): iterable
    {
        if (!isset($this->repositories[$repo])) {
            $this->repositories[$repo] = $this->subscriberRepository->findForRealtimeRepo($repo);
        }

        foreach ($this->repositories[$repo] as $id) {
            if (!isset($this->subscribers[$id->value])) {
                $this->subscribers[$id->value] = $this->subscriberRepository->find($id);
            }

            yield $this->subscribers[$id->value];
        }
    }
}
