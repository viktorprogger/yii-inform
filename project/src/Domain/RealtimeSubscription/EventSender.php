<?php

namespace Viktorprogger\YiisoftInform\Domain\RealtimeSubscription;

use Psr\Log\LoggerInterface;
use Viktorprogger\TelegramBot\Domain\Client\MessageFormat;
use Viktorprogger\TelegramBot\Domain\Client\TelegramClientInterface;
use Viktorprogger\TelegramBot\Domain\Client\TooManyRequestsException;
use Viktorprogger\TelegramBot\Domain\Client\WrongEntitiesException;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;

final class EventSender
{
    private const MAX_ATTEMPTS = 10;

    public function __construct(
        private readonly TelegramMessageGenerator $generator,
        private readonly TelegramClientInterface $client,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function send(GithubEvent $event, Subscriber $subscriber, int $attempts = 0): void
    {
        $this->logger->info(
            'Sending event {eventId} to subscriber {subscriberId}',
            ['subscriberId' => $subscriber->id->value, 'eventId' => $event->id->value]
        );

        $message = $this->generator->generateForEvent($event, $subscriber);
        try {
            try {
                $this->client->sendMessage($message);
            } catch (WrongEntitiesException) {
                $this->client->sendMessage(
                    $message
                        ->withFormat(MessageFormat::TEXT)
                        ->withText(<<<MSG
                            Message markup has errors, so it was sent in a raw style.
                            Feel free to create a ticket or to subscribe to an existing one here:
                            https://github.com/viktorprogger/yii-inform/issues

                            Original message text:
                            $message->text
                            MSG
                        )
                );
            }
        } catch (TooManyRequestsException) {
            if ($attempts < self::MAX_ATTEMPTS) {
                usleep(300000);
                $this->send($event, $subscriber, ++$attempts);
            }
        }
    }
}
