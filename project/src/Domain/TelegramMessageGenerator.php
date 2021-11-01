<?php

namespace Yiisoft\Inform\Domain;

use Yiisoft\Inform\Domain\Entity\Event\EventType;
use Yiisoft\Inform\Domain\Entity\Event\SubscriptionEvent;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\RepositoryButtonService;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;

final class TelegramMessageGenerator
{
    public function __construct(private readonly RepositoryButtonService $buttonService)
    {
    }

    public function generateForEvent(SubscriptionEvent $subscriptionEvent, string $chatId, Subscriber $subscriber): TelegramMessage
    {
        return match ($subscriptionEvent->type) {
            EventType::ISSUE_OPENED => $this->issueCreated($subscriptionEvent->payload, $chatId),
            EventType::ISSUE_CLOSED => $this->issueClosed($subscriptionEvent->payload, $chatId),
            EventType::ISSUE_REOPENED => $this->issueReopened($subscriptionEvent->payload, $chatId),
            EventType::ISSUE_COMMENTED => $this->issueCommented($subscriptionEvent->payload, $chatId),
            EventType::PR_OPENED => $this->prOpened($subscriptionEvent->payload, $chatId),
            EventType::PR_CLOSED => $this->prClosed($subscriptionEvent->payload, $chatId),
            EventType::PR_MERGED => $this->prMerged($subscriptionEvent->payload, $chatId),
            EventType::PR_REOPENED => $this->prReopened($subscriptionEvent->payload, $chatId),
            EventType::PR_CHANGED => $this->prChanged($subscriptionEvent->payload, $chatId),
            EventType::PR_COMMENTED => $this->prCommented($subscriptionEvent->payload, $chatId),
            EventType::PR_MERGE_APPROVED => $this->prMergeApproved($subscriptionEvent->payload, $chatId),
            EventType::PR_MERGE_DECLINED => $this->prMergeDeclined($subscriptionEvent->payload, $chatId),
            EventType::NEW_REPO => $this->newRepoCreated($subscriptionEvent->payload, $chatId),
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
            $this->buttonService->createButton('repo_name', $subscriber, SubscriptionType::REALTIME, 'repo_name'),
            $this->buttonService->createButton('repo_name', $subscriber, SubscriptionType::SUMMARY, 'repo_name'),
        ]];

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }
}
