<?php

declare(strict_types=1);

use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\Infrastructure\Entity\Subscriber\SubscriberIdFactory;
use Yiisoft\Inform\Infrastructure\Entity\Subscriber\SubscriberRepository;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Client\TelegramClientLog;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Client\TelegramClientSymfony;

return [
    TelegramClientInterface::class => TelegramClientSymfony::class,
    TelegramClientSymfony::class => ['__construct()' => ['token' => getenv('BOT_TOKEN')]],
    HttpClientInterface::class => static fn() => HttpClient::create(),
    SubscriberIdFactoryInterface::class => SubscriberIdFactory::class,
    SubscriberRepositoryInterface::class => SubscriberRepository::class,
    UuidFactoryInterface::class => UuidFactory::class,
];
