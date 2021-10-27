<?php

namespace Yiisoft\Inform\Domain;

// TODO: divide into interface and infrastructure
use Github\Client;
use Psr\SimpleCache\CacheInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;

final class GithubService
{
    private const CACHE_KEY = 'gh-yii3-repo-list';

    public function __construct(
        private readonly Client $api,
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
}
