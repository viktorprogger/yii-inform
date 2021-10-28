<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\Domain\GithubRepository\GithubRepositoryInterface;
use Yiisoft\Inform\Infrastructure\Entity\GithubRepository\GithubRepository;
use Yiisoft\Inform\Infrastructure\Entity\Subscriber\SubscriberIdFactory;
use Yiisoft\Inform\Infrastructure\Entity\Subscriber\SubscriberRepository;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Router;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Client\TelegramClientLog;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Client\TelegramClientSymfony;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;

/** @var array $params */

return [
    TelegramClientInterface::class => TelegramClientSymfony::class,
    TelegramClientSymfony::class => ['__construct()' => ['token' => getenv('BOT_TOKEN')]],
    HttpClientInterface::class => static fn() => HttpClient::create(),
    SubscriberIdFactoryInterface::class => SubscriberIdFactory::class,
    SubscriberRepositoryInterface::class => SubscriberRepository::class,
    UuidFactoryInterface::class => UuidFactory::class,
    LoggerInterface::class => Logger::class,
    Logger::class => static fn(FileTarget $target) => new Logger([$target]),
    CacheInterface::class => FileCache::class,
    GithubRepositoryInterface::class => GithubRepository::class,
    Router::class => [
        '__construct()' => ['routes' => $params['telegram routes']]
    ]
];
