<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\GithubEventMessage\Markdown;

use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\GithubService;

final class MarkdownMessageFormatter
{
    public function __construct(private readonly GithubService $github)
    {
    }

    public function getEventText(GithubEvent $event): string
    {
        return match ($event->type) {
            EventType::ISSUE_OPENED => $this->issueCreated(),
            EventType::ISSUE_CLOSED => $this->issueClosed(),
            EventType::ISSUE_REOPENED => $this->issueReopened(),
            EventType::ISSUE_COMMENTED => $this->issueCommented(),
            EventType::PR_OPENED => $this->prOpened(),
            EventType::PR_CLOSED => $this->prClosed(),
            EventType::PR_MERGED => $this->prMerged(),
            EventType::PR_REOPENED => $this->prReopened(),
            EventType::PR_CHANGED => $this->prChanged(),
            EventType::PR_COMMENTED => $this->prCommented(),
            EventType::PR_MERGE_APPROVED => $this->prMergeApproved(),
            EventType::PR_MERGE_DECLINED => $this->prMergeDeclined(),
            EventType::NEW_REPO => $this->newRepoCreated(),
        };
    }

    private function issueCreated(): string
    {
        return "❓ Был создан тикет {!issue!} пользователем {!issue_author!}\.";
    }

    private function issueClosed(): string
    {
        return "❎ Закрыт тикет {!issue!} пользователем {!issue_closed_user!}\.";
    }

    private function issueReopened(): string
    {
        return "🚪 Заново открыт тикет {!issue!}\.";
    }

    private function issueCommented(): string
    {
        return <<<MD
            📝 В тикет {!issue!} добавлен комментарий\.
            Автор: {!comment_author!}
            Текст:
            {!comment_text!}
            MD;
    }

    private function prOpened(): string
    {
        return "❗ Открыт PR {!pr!} пользователем {!pr_author!}\.";
    }

    private function prClosed(): string
    {
        return "❌ Закрыт PR {!pr!}\.";
    }

    private function prMerged(): string
    {
        return "🔥 Смержен PR {!pr!}\.";
    }

    private function prReopened(): string
    {
        return "🚪 Заново открыт PR {!pr!}\.";
    }

    private function prChanged(): string
    {
        // TODO
        return <<<MD
             В PR {!pr!} произошли изменения\.
            \{changes_summary\}
            MD;
    }

    private function prCommented(): string
    {
        return <<<MD
            📝 В PR {!pr!} добавлен комментарий\.
            Автор: {!comment_author!}
            Текст:
            {!comment_text!}
            MD;
    }

    private function prMergeApproved(): string
    {
        return "✅ Мёрж пулл реквеста {!pr!} одобрен пользователем {!review_user!}\.";
    }

    private function prMergeDeclined(): string
    {
        return "✏ Пулл реквест {!pr!} требует изменений по мнению пользователя {!review_user!}\.";
    }

    private function newRepoCreated(): string
    {
        return "🥳 Создан новый репозиторий {!repo_full!}";
    }

    public function template(string $message, GithubEvent $event): string
    {
        $payload = $event->payload;

        $callbacks = [
            '/{!repo!}/' => fn (): string => $this->repoNameClear($event->repo),
            '/{!repo_full!}/' => fn (): string => $this->markdownTextClear($event->repo),
            '/{!issue!}/' => fn (): string => "[\#{$payload['issue']['number']} {$this->markdownTextClear($payload['issue']['title'])}]({$payload['issue']['html_url']})",
            '/{!issue_author!}/' => static fn (): string => "[{$payload['issue']['user']['login']}]({$payload['issue']['user']['html_url']})",
            '/{!issue_closed_user!}/' => function() use($event, $payload): string {
                if (!isset($payload['closed_by'])) {
                    $event = $this->github->enrich($event);
                }

                $payload = $event->payload;
                if (isset($payload['closed_by'])) {
                    return "[{$payload['closed_by']['login']}]({$payload['closed_by']['html_url']})";
                }

                /** @noinspection HtmlUnknownAttribute */
                return '`\\<not found\\>`';
            },
            '/{!comment_author!}/' => static fn (): string => "[{$payload['comment']['user']['login']}]({$payload['comment']['user']['html_url']})",
            '/{!comment_text!}/' => fn (): string => $this->markdownTextClear($payload['comment']['body']),
            '/{!pr!}/' => fn (): string => "[\#{$payload['pull_request']['number']} {$this->markdownTextClear($payload['pull_request']['title'])}]({$payload['pull_request']['html_url']})",
            '/{!pr_author!}/' => static fn (): string => "[{$payload['pull_request']['user']['login']}]({$payload['pull_request']['user']['html_url']})",
            '/{!review_user!}/' => static fn (): string => "[{$payload['review']['user']['login']}]({$payload['review']['user']['html_url']})",
        ];

        /** @var string $result */
        $result = preg_replace_callback_array($callbacks, $message);

        return $result;
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

    private function markdownTextClear(string $text): string
    {
        $result = str_replace('\\', '\\\\', $text);

        $result = preg_replace_callback(
            '/(?:\[.+]\([-\w@:%._+~#=&]+\))+|[()\[\]]/m',
            static fn($matches) => strlen($matches[0]) === 1 ? '\\' . $matches[0] : $matches[0],
            $result,
        );

        $result = preg_replace(
            '/\S(_)/',
            '\\\\$1',
            $result,
        );

        return preg_replace(
            '/([-.#{}%&+<>=!,:^])/',
            '\\\\$1',
            $result,
        );
    }
}
