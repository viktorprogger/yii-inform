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
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['issue']['title']);

        $text = "\#$repo\n";
        $text .= "Был создан тикет [\#{$event->payload['issue']['number']} $title]({$event->payload['issue']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueClosed(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['issue']['title']);

        $text = "\#$repo\n";
        $text .= "Закрыт тикет [\#{$event->payload['issue']['number']} $title]({$event->payload['issue']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueReopened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['issue']['title']);

        $text = "\#$repo\n";
        $text .= "Заново открыт тикет [\#{$event->payload['issue']['number']} $title]({$event->payload['issue']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function issueCommented(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['issue']['title']);
        $comment = $this->markdownTextClear($event->payload['comment']['body']);

        $text = <<<MD
            \#$repo
            В тикет [\#{$event->payload['issue']['number']} $title]({$event->payload['issue']['html_url']}) добавлен комментарий\.
            Автор: [{$event->payload['comment']['user']['login']}]({$event->payload['comment']['user']['html_url']})
            Текст:
            $comment
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prOpened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);

        $text = "\#$repo\n";
        $text .= "Открыт PR [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prClosed(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);

        $text = "\#$repo\n";
        $text .= "Закрыт PR [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMerged(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);

        $text = "\#$repo\n";
        $text .= "Смержили PR [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prReopened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);

        $text = "\#$repo\n";
        $text .= "Заново открыт PR [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prChanged(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);
        // TODO

        $text = <<<MD
            \#$repo
             В PR [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']}) произошли изменения\.
            \{changes_summary\}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prCommented(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);
        $comment = $this->markdownTextClear($event->payload['comment']['body']);

        $text = <<<MD
            \#$repo
             В PR [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']}) добавлен комментарий\.
            Автор: [{$event->payload['comment']['user']['login']}]({$event->payload['comment']['user']['html_url']})
            Текст:
            $comment
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMergeApproved(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);

        $text = "\#$repo\n";
        $text .= "Мёрж пулл реквеста [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']}) одобрен пользователем [{$event->payload['review']['user']['login']}]({$event->payload['review']['user']['html_url']})\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function prMergeDeclined(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);
        $title = $this->markdownTextClear($event->payload['pull_request']['title']);

        $text = "\#$repo\n";
        $text .= "Для пулл реквеста [\#{$event->payload['pull_request']['number']} $title]({$event->payload['pull_request']['html_url']}) по итогам ревью кода требуются изменения\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId);
    }

    private function newRepoCreated(GithubEvent $event, string $chatId): TelegramMessage
    {
        $repo = $this->repoNameClear($event->repo);

        $text = "\#$repo\n";
        $text .= "Создан новый репозиторий {$this->markdownTextClear($event->repo)}";

        $keyboard = [[
            $this->formatter->createInlineButton($event->repo, ButtonAction::ADD, SubscriptionType::REALTIME, 'Подписаться realtime'),
            $this->formatter->createInlineButton($event->repo, ButtonAction::ADD, SubscriptionType::SUMMARY, 'Подписаться на summary'),
        ]];

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, $keyboard);
    }

    /**
     * @param string $text
     *
     * @return string|array|null
     */
    private function repoNameClear(string $text): string|array|null
    {
        return preg_replace_callback(
            '/-([\w])/',
            static fn($matches) => strtoupper($matches[1]),
            $text
        );
    }

    /**
     * @param string $text
     *
     * @return string|array|null
     */
    private function markdownTextClear(string $text): string|array|null
    {
        return preg_replace(
            '/([-.#{%&+])/',
            '\\\\$1',
            $text,
        );
    }
}
