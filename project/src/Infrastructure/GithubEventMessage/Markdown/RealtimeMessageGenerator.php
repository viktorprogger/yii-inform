<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\GithubEventMessage\Markdown;

use Viktorprogger\TelegramBot\Domain\Client\MessageFormat;
use Viktorprogger\TelegramBot\Domain\Client\TelegramMessage;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Domain\RealtimeSubscription\RealtimeMessageGeneratorInterface;
use Viktorprogger\YiisoftInform\Domain\SubscriptionType;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\ButtonAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;

final class RealtimeMessageGenerator implements RealtimeMessageGeneratorInterface
{
    public function __construct(
        private readonly Formatter $keyboardFormatter,
        private readonly MarkdownMessageFormatter $messageFormatter,
    ) {
    }

    public function generateForEvent(GithubEvent $event, Subscriber $subscriber): TelegramMessage
    {
        $message = new TelegramMessage($this->getMessageText($event), MessageFormat::MARKDOWN, $subscriber->chatId);

        if ($event->type === EventType::NEW_REPO) {
            $message->withKeyboard(
                [
                    [
                        $this->keyboardFormatter->createInlineButton(
                            $event->repo,
                            ButtonAction::ADD,
                            SubscriptionType::REALTIME,
                            1,
                            'Подписаться realtime'
                        ),
                        $this->keyboardFormatter->createInlineButton(
                            $event->repo,
                            ButtonAction::ADD,
                            SubscriptionType::SUMMARY,
                            1,
                            'Подписаться на summary'
                        ),
                    ],
                ]
            );
        }

        return $message;
    }

    private function getMessageText(GithubEvent $event): string
    {
        $text = "\#{!repo!}\n";
        $text .= $this->messageFormatter->getEventText($event);

        return $this->messageFormatter->template($text, $event);
    }
}
