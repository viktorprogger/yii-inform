<?php

namespace Yiisoft\Inform\Domain\RealtimeSubscription;

use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

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
