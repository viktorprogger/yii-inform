<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Client\Client;

final class GithubService
{
    private const BOTS = [
        'dependabot[bot]',
        'codecov[bot]',
    ];

    public function __construct(
        private readonly Client $api,
        private readonly EventIdFactoryInterface $eventIdFactory,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function enrich(GithubEvent $event): GithubEvent
    {
        $url = match(true) {
            $event->type->isIssueRelated() => $event->payload['issue']['url'],
            $event->type->isPRRelated() => $event->payload['pull_request']['url'],
            default => null,
        };

        if ($url !== null) {
            $response = $this->api->getHttpClient()->get($url);
            if ($response->getStatusCode() === 200) {
                /**
                 * @noinspection PhpUnhandledExceptionInspection
                 * @noinspection JsonEncodingApiUsageInspection
                 */
                return $this->eventRepository->enrich(
                    $event,
                    json_decode(
                        (string) $response->getBody(),
                        true,
                        flags: JSON_THROW_ON_ERROR
                    )
                );
            }
        }

        return $event;
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
        $events = $this->api->organization()->events('yiisoft', ['per_page' => 500]);
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
        $payload = $data['payload'];

        return match ($data['type']) {
            'IssuesEvent' => match ($payload['action']) {
                'opened' => EventType::ISSUE_OPENED,
                'closed' => EventType::ISSUE_CLOSED,
                'reopened' => EventType::ISSUE_REOPENED,
                default => null,
            },
            'IssueCommentEvent' => match ($payload['action']) {
                'created' => EventType::ISSUE_COMMENTED,
                default => null,
            },
            'PullRequestEvent' => match ($payload['action']) {
                'opened' => EventType::PR_OPENED,
                'closed' => $payload['pull_request']['closed_at'] === $payload['pull_request']['merged_at']
                    ? EventType::PR_MERGED
                    : EventType::PR_CLOSED,
                'reopened' => EventType::PR_REOPENED,
                'edited' => EventType::PR_CHANGED,
                default => null,
            },
            'PullRequestReviewEvent' => match ($payload['review']['state']) {
                'approved' => EventType::PR_MERGE_APPROVED,
                default => EventType::PR_MERGE_DECLINED,
            },
            'PullRequestReviewCommentEvent' => match ($payload['action']) {
                'created' => EventType::PR_COMMENTED,
                default => null,
            },
            default => null,
        };
    }
}
