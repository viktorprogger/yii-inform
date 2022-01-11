<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Client\Client;

final class GithubService
{
    private const BOTS = [
        'dependabot[bot]',
    ];

    public function __construct(
        private readonly Client $api,
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

        $this->logger->info('Load events request');
        $events = $this->api->organization()->events('yiisoft', ['per_page' => 100]);
        $this->logger->info('Load events response', ['eventsCount' => count($events)]);

        foreach (array_reverse($events) as $eventData) {
            $repo = str_replace('yiisoft/', '', $eventData['repo']['name']);

            if (in_array($repo, $repositories, true) === false) {
                continue;
            }

            if (in_array($eventData['actor']['login'], self::BOTS, true)) {
                continue;
            }

            $id = $this->eventIdFactory->create((string) $eventData['id']);
            if ($this->eventRepository->exists($id)) {
                continue;
            }

            $type = $this->getEventType($eventData);
            if ($type !== null) {
                $this->logger->info('Found new event', ['eventData' => $eventData]);
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
                'approved' => EventType::PR_MERGE_APPROVED,
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
