<?php

namespace Viktorprogger\YiisoftInform\Domain\RealtimeSubscription;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\ButtonAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\GithubService;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Arrays\ArrayHelper;

final class TelegramMessageGenerator
{
    public function __construct(
        private readonly Formatter $formatter,
        private readonly GithubService $github,
    )
    {
    }

    public function generateForEvent(GithubEvent $event, Subscriber $subscriber): TelegramMessage
    {
        // FIXME take a Subscriber instead of chatId to get method newRepoCreated() done
        $message = match ($event->type) {
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

        return $message->withText($this->template($message->text, $event));
    }

    private function issueCreated(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Был создан тикет {!issue!} пользователем {!issue_author!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function issueClosed(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Закрыт тикет {!issue!} пользователем {!issue_closed_user!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function issueReopened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Заново открыт тикет {!issue!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function issueCommented(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = <<<MD
            \#{!repo!}
            В тикет {!issue!} добавлен комментарий\.
            Автор: {!comment_author!}
            Текст:
            {!comment_text!}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prOpened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Открыт PR {!pr!} пользователем {!pr_author!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prClosed(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Закрыт PR {!pr!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prMerged(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Смержен PR {!pr!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prReopened(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Заново открыт PR {!pr!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prChanged(GithubEvent $event, string $chatId): TelegramMessage
    {
        // TODO
        $text = <<<MD
            \#{!repo!}
             В PR {!pr!} произошли изменения\.
            \{changes_summary\}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prCommented(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = <<<MD
            \#{!repo!}
             В PR {!pr!} добавлен комментарий\.
            Автор: {!comment_author!}
            Текст:
            {!comment_text!}
            MD;

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prMergeApproved(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "✅ Мёрж пулл реквеста {!pr!} одобрен пользователем {!review_user!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function prMergeDeclined(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "❌ Пулл реквест {!pr!} требует изменений по мнению пользователя {!review_user!}\.";

        return new TelegramMessage($text, MessageFormat::markdown(), $chatId, disableLinkPreview: true);
    }

    private function newRepoCreated(GithubEvent $event, string $chatId): TelegramMessage
    {
        $text = "\#{!repo!}\n";
        $text .= "Создан новый репозиторий {!repo_full!}";

        $keyboard = [
            [
                $this->formatter->createInlineButton(
                    $event->repo,
                    ButtonAction::ADD,
                    SubscriptionType::REALTIME,
                    1,
                    'Подписаться realtime'
                ),
                $this->formatter->createInlineButton(
                    $event->repo,
                    ButtonAction::ADD,
                    SubscriptionType::SUMMARY,
                    1,
                    'Подписаться на summary'
                ),
            ]
        ];

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
        $result = preg_replace_callback(
            '/(?:\[.+]\([-\w@:%._+~#=&]+\))+|[()\[\]]/m',
            static fn($matches) => strlen($matches[0]) === 1 ? '\\' . $matches[0] : $matches[0],
            $text,
        );

        $result = preg_replace(
            '/\S(_)/',
            '\\\\$1',
            $result,
        );

        return preg_replace(
            '/([-.#{%&+<>=])/',
            '\\\\$1',
            $result,
        );
    }

    private function template(string $message, GithubEvent $event): string
    {
        $payload = $event->payload;

        $callbacks = [
            '/{!repo!}/' => fn () => $this->repoNameClear($event->repo),
            '/{!repo_full!}/' => fn () => $this->markdownTextClear($event->repo),
            '/{!issue!}/' => fn () => "[\#{$payload['issue']['number']} {$this->markdownTextClear($event->payload['issue']['title'])}]({$payload['issue']['html_url']})",
            '/{!issue_author!}/' => static fn () => "[{$payload['issue']['user']['login']}]({$payload['issue']['user']['html_url']})",
            '/{!issue_closed_user!}/' => function() use($event) {
                if (!isset($event->payload['closed_by'])) {
                    $event = $this->github->enrich($event);
                }

                $payload = $event->payload;
                if (isset($payload['closed_by'])) {
                    return "[{$payload['closed_by']['login']}]({$payload['closed_by']['html_url']})";
                }

                return '`\\<not found>`';
            },
            '/{!comment_author!}/' => static fn () => "[{$event->payload['comment']['user']['login']}]({$event->payload['comment']['user']['html_url']})",
            '/{!comment_text!}/' => fn() => $this->markdownTextClear($event->payload['comment']['body']),
            '/{!pr!}/' => fn () => "[\#{$event->payload['pull_request']['number']} {$this->markdownTextClear($event->payload['pull_request']['title'])}]({$event->payload['pull_request']['html_url']})",
            '/{!pr_author!}/' => static fn () => "[{$payload['pull_request']['user']['login']}]({$payload['pull_request']['user']['html_url']})",
            '/{!review_user!}/' => static fn () => "[{$payload['pull_request']['user']['login']}]({$payload['pull_request']['user']['html_url']})",
        ];

        return preg_replace_callback_array($callbacks, $message);
    }
}
