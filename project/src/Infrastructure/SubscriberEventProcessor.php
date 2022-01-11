<?php

namespace Viktorprogger\YiisoftInform\Infrastructure;

use Psr\Log\LoggerInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberId;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Queue\RealtimeEventMessage;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventCreatedEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Yiisoft\Yii\Queue\Queue;

final class SubscriberEventProcessor
{
    private array $repositories = [];

    public function __construct(
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly Queue $queue,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handle(EventCreatedEvent $event): void
    {
        $this->sendRealtimeSubscribers($event->subscriptionEvent);
    }

    public function sendRealtimeSubscribers(GithubEvent $subscriptionEvent): void
    {
        try {
            if ($subscriptionEvent->type === EventType::NEW_REPO) {
                $ids = $this->subscriberRepository->getAllIds();
            } else {
                $ids = $this->getSubscribers($subscriptionEvent->repo);
            }
            $this->logger->info(
                'Found {subscriberCount} subscribers for event {eventId}',
                ['subscriberCount' => count($ids), 'eventId' => $subscriptionEvent->id]
            );

            foreach ($ids as $subscriberId) {
                $this->queue->push(new RealtimeEventMessage($subscriptionEvent->id->value, $subscriberId->value));
            }
        } finally {
            $this->repositories = [];
        }
    }

    /**
     * @param string $repo
     *
     * @return SubscriberId[]
     */
    private function getSubscribers(string $repo): array
    {
        if (!isset($this->repositories[$repo])) {
            $this->repositories[$repo] = $this->subscriberRepository->findForRealtimeRepo($repo);
        }

        return $this->repositories[$repo];
    }
}
