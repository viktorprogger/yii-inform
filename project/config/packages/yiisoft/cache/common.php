<?php

declare(strict_types=1);

use Psr\SimpleCache\CacheInterface;
use Yiisoft\Cache\File\FileCache;

return [
    \Yiisoft\Cache\CacheInterface::class => \Yiisoft\Cache\Cache::class,
    CacheInterface::class => FileCache::class,
];
