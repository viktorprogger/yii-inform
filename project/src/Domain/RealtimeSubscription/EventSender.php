<?php

namespace Viktorprogger\YiisoftInform\Domain\RealtimeSubscription;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

final class EventSender
{
    public function __construct(
        private readonly TelegramMessageGenerator $generator,
        private readonly TelegramClientInterface $client,
    ) {
    }

    public function send(GithubEvent $event, Subscriber $subscriber): void
    {
        $this->client->sendMessage($this->generator->generateForEvent($event, $subscriber));
    }
}
