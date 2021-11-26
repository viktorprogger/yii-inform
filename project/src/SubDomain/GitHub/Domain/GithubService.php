<?php

namespace Yiisoft\Inform\SubDomain\GitHub\Domain;

use DateTimeImmutable;
use Github\Client;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;

final class GithubService
{
    private const BOTS = [
        'dependabot[bot]',
    ];

    public function __construct(
        private readonly Client $api,
        private readonly HttpClientInterface $httpClient,
        private readonly EventIdFactoryInterface $eventIdFactory,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function loadYii3Packages(): array
    {
        $page = 1;
        $result = [];
        do {
            $repositories = $this->api->repositories()->org('yiisoft', ['page' => $page++, 'per_page' => 200]);
            foreach ($repositories as $repository) {
                if (in_array('yii3', $repository['topics'], true)) {
                    $result[] = $repository['name'];
                }
            }
        } while ($repositories !== []);

        sort($result);

        return $result;
    }

    public function loadEvents(string ...$repositories): void
    {
        if ($repositories === []) {
            return;
        }

        $events = $this->httpClient
            ->request('GET', 'https://api.github.com/orgs/yiisoft/events?per_page=100&page=1')
            ->getContent();
        $events = json_decode($events, true, flags: JSON_THROW_ON_ERROR);

        foreach ($events as $eventData) {
            $repo = str_replace('yiisoft/', '', $eventData['repo']['name']);

            if (in_array($repo, $repositories, true) === false) {
                continue;
            }

            if (in_array($repo, self::BOTS, true)) {
                continue;
            }

            $this->logger->debug($eventData);

            $id = $this->eventIdFactory->create((string) $eventData['id']);
            if ($this->eventRepository->exists($id)) {
                continue;
            }

            $type = $this->getEventType($eventData);
            if ($type !== null) {
                $event = new GithubEvent($id, $type, $repo, $eventData['payload'], new DateTimeImmutable());
                $this->eventRepository->create($event);
            }
        }
    }

    private function getEventType(array $data): ?EventType
    {
        return match ($data['type']) {
            'IssuesEvent' => match ($data['payload']['action']) {
                'opened' => EventType::ISSUE_OPENED,
                'closed' => EventType::ISSUE_CLOSED,
                'reopened' => EventType::ISSUE_REOPENED,
                default => null,
            },
            'IssueCommentEvent' => match ($data['payload']['action']) {
                'created' => EventType::ISSUE_COMMENTED,
                default => null,
            },
            'PullRequestEvent' => match ($data['payload']['action']) {
                'opened' => EventType::PR_OPENED,
                'closed' => EventType::PR_CLOSED, // TODO add merge check https://docs.github.com/en/rest/reference/pulls#check-if-a-pull-request-has-been-merged
                'reopened' => EventType::PR_REOPENED,
                'edited' => EventType::PR_CHANGED,
                default => null,
            },
            'PullRequestReviewEvent' => match ($data['payload']['review']['state']) {
                'APPROVED' => EventType::PR_MERGE_APPROVED,
                default => EventType::PR_MERGE_DECLINED,
            },
            'PullRequestReviewCommentEvent' => match ($data['payload']['action']) {
                'created' => EventType::PR_COMMENTED,
                default => null,
            },
            default => null,
        };
    }
}
