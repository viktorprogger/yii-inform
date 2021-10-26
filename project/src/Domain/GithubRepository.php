<?php

namespace Yiisoft\Inform\Domain;

// TODO: divide into interface and infrastructure
use Github\Client;
use Psr\SimpleCache\CacheInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;

final class GithubRepository
{
    private const CACHE_KEY = 'gh-yii3-repo-list';

    public function __construct(
        private Client $github,
        private CacheInterface $cache,
    ) {
    }

    public function getYii3Packages(): array
    {
        $result = $this->cache->get(self::CACHE_KEY, []);
        if ($result === []) {
            $result = $this->loadYii3Packages();
            $this->cache->set(self::CACHE_KEY, $result);
        }

        sort($result); // TODO remove

        return $result;
    }

    public function loadYii3Packages(): array
    {
        $page = 1;
        $result = [];
        do {
            $repositories = $this->github->repositories()->org('yiisoft', ['page' => $page++, 'per_page' => 200]);
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
