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

    public function generateForEvent(GithubEvent $subscriptionEvent, Subscriber $subscriber): TelegramMessage
    {
        // FIXME take a Subscriber instead of chatId to get method newRepoCreated() done
        return match ($subscriptionEvent->type) {
            EventType::ISSUE_OPENED => $this->issueCreated($subscriptionEvent->payload, $subscriber->chatId),
            EventType::ISSUE_CLOSED => $this->issueClosed($subscriptionEvent->payload, $subscriber->chatId),
            EventType::ISSUE_REOPENED => $this->issueReopened($subscriptionEvent->payload, $subscriber->chatId),
            EventType::ISSUE_COMMENTED => $this->issueCommented($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_OPENED => $this->prOpened($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_CLOSED => $this->prClosed($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_MERGED => $this->prMerged($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_REOPENED => $this->prReopened($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_CHANGED => $this->prChanged($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_COMMENTED => $this->prCommented($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_MERGE_APPROVED => $this->prMergeApproved($subscriptionEvent->payload, $subscriber->chatId),
            EventType::PR_MERGE_DECLINED => $this->prMergeDeclined($subscriptionEvent->payload, $subscriber->chatId),
            EventType::NEW_REPO => $this->newRepoCreated($subscriptionEvent->payload, $subscriber->chatId),
        };
    }

    private function issueCreated(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: был создан тикет [{issue_name}]({issue_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueClosed(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: закрыт тикет [{issue_name}]({issue_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueReopened(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: заново открыт тикет [{issue_name}]({issue_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueCommented(array $payload, string $chatId): TelegramMessage
    {
        $text = <<<MD
            {repo_name}: в тикет [{issue_name}]({issue_link}) добавлен комментарий
            _{comment_text}_
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prOpened(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: открыт PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prClosed(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: закрыт PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMerged(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: смержили PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prReopened(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: заново открыт PR [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prChanged(array $payload, string $chatId): TelegramMessage
    {
        $text = <<<MD
            {repo_name}: в PR [{pr_name}]({pr_link}) произошли изменения.
            {changes_summary}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prCommented(array $payload, string $chatId): TelegramMessage
    {
        $text = <<<MD
            {repo_name}: в PR [{pr_name}]({pr_link}) добавлен комментарий
            {PR_comment}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMergeApproved(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: Одобрен мёрж пулл реквеста [{pr_name}]({pr_link}).";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMergeDeclined(array $payload, string $chatId): TelegramMessage
    {
        $text = "{repo_name}: для пулл реквеста [{pr_name}]({pr_link}) требуются изменения.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function newRepoCreated(array $payload, string $chatId): TelegramMessage
    {
        $text = "Создан новый репозиторий: {repo_name}";
        $keyboard = [[
            $this->formatter->createInlineButton('{repo_name}', ButtonAction::ADD, SubscriptionType::REALTIME, 'Подписаться realtime'),
            $this->formatter->createInlineButton('{repo_name}', ButtonAction::ADD, SubscriptionType::SUMMARY, 'Подписаться на summary'),
        ]];

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, $keyboard);
    }
}
