<?php

namespace Yiisoft\Inform\Domain;

use Yiisoft\Inform\Domain\Entity\Event\SubscriptionEvent;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

class RealtimeEventSender
{
    public function __construct(
        private readonly TelegramMessageGenerator $generator,
        private readonly TelegramClientInterface $client,
    ) {
    }

    public function send(SubscriptionEvent $event, Subscriber $subscriber): void
    {
        $this->client->sendMessage($this->generator->generateForEvent($event, $subscriber->chatId));
    }
}
