<?php

namespace Viktorprogger\YiisoftInform\Domain\RealtimeSubscription;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramRequestException;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TooManyRequestsException;

final class EventSender
{
    private const MAX_ATTEMPTS = 10;

    public function __construct(
        private readonly TelegramMessageGenerator $generator,
        private readonly TelegramClientInterface $client,
    ) {
    }

    public function send(GithubEvent $event, Subscriber $subscriber, int $attempts = 0): void
    {
        try {
            $this->client->sendMessage($this->generator->generateForEvent($event, $subscriber));
        } catch (TooManyRequestsException) {
            if ($attempts < self::MAX_ATTEMPTS) {
                usleep(300000);
                $this->send($event, $subscriber, ++$attempts);
            }
        }
    }
}
