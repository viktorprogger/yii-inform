<?php

namespace Yiisoft\Inform\Domain;

use Github\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GithubService
{
    private const CACHE_KEY = 'gh-yii3-repo-list';

    public function __construct(
        private readonly Client $api,
        private readonly HttpClientInterface $httpClient,
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

    public function loadEvents(string ...$repositories): array
    {
        if ($repositories === []) {
            return [];
        }

        $events = $this->httpClient
            ->request('GET', 'https://api.github.com/orgs/yiisoft/events?per_page=100&page=1')
            ->getContent();
        $events = json_decode($events, true, flags: JSON_THROW_ON_ERROR);

        foreach ($events as $event) {
            $repo = str_replace('yiisoft/', '', $event['repo']['name']);

            if (in_array($repo, $repositories, true) === false) {
                continue;
            }

            if (in_array($repo, $repositories, true) === false) {
                continue;
            }

        }

        return [];
    }
}
