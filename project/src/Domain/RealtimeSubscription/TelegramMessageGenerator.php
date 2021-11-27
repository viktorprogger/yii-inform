<?php

namespace Viktorprogger\YiisoftInform\Domain\RealtimeSubscription;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\ButtonAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;

final class TelegramMessageGenerator
{
    public function __construct(private readonly Formatter $formatter)
    {
    }

    public function generateForEvent(GithubEvent $event, Subscriber $subscriber): TelegramMessage
    {
        // FIXME take a Subscriber instead of chatId to get method newRepoCreated() done
        return match ($event->type) {
            EventType::ISSUE_OPENED => $this->issueCreated($event, $subscriber->chatId),
            EventType::ISSUE_CLOSED => $this->issueClosed($event, $subscriber->chatId),
            EventType::ISSUE_REOPENED => $this->issueReopened($event, $subscriber->chatId),
            EventType::ISSUE_COMMENTED => $this->issueCommented($event, $subscriber->chatId),
            EventType::PR_OPENED => $this->prOpened($event, $subscriber->chatId),
            EventType::PR_CLOSED => $this->prClosed($event, $subscriber->chatId),
            EventType::PR_MERGED => $this->prMerged($event, $subscriber->chatId),
            EventType::PR_REOPENED => $this->prReopened($event, $subscriber->chatId),
            EventType::PR_CHANGED => $this->prChanged($event, $subscriber->chatId),
            EventType::PR_COMMENTED => $this->prCommented($event, $subscriber->chatId),
            EventType::PR_MERGE_APPROVED => $this->prMergeApproved($event, $subscriber->chatId),
            EventType::PR_MERGE_DECLINED => $this->prMergeDeclined($event, $subscriber->chatId),
            EventType::NEW_REPO => $this->newRepoCreated($event, $subscriber->chatId),
        };
    }

    private function issueCreated(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Был создан тикет [{issue_name}]({issue_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueClosed(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Закрыт тикет [{issue_name}]({issue_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueReopened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Заново открыт тикет [{issue_name}]({issue_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueCommented(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = <<<MD
            #$event->repo
            В тикет [{issue_name}]({issue_link}) добавлен комментарий
            _{comment_text}_
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prOpened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Открыт PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prClosed(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Закрыт PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMerged(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Смержили PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prReopened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Заново открыт PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prChanged(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = <<<MD
            #$event->repo
             В PR [{pr_name}]({pr_link}) произошли изменения.
            {changes_summary}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prCommented(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = <<<MD
            #$event->repo
             В PR [{pr_name}]({pr_link}) добавлен комментарий
            {PR_comment}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMergeApproved(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Одобрен мёрж пулл реквеста [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMergeDeclined(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "#$event->repo\n";
        $text .= "Для пулл реквеста [{pr_name}]({pr_link}) требуются изменения.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function newRepoCreated(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "Создан новый репозиторий $event->repo";
        $keyboard = [[
            $this->formatter->createInlineButton($event->repo, ButtonAction::ADD, SubscriptionType::REALTIME, 'Подписаться realtime'),
            $this->formatter->createInlineButton($event->repo, ButtonAction::ADD, SubscriptionType::SUMMARY, 'Подписаться на summary'),
        ]];

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, $keyboard);
    }
}
