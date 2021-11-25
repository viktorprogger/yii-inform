<?php

declare(strict_types=1);

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Inform\Domain\Entity\Event\EventIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Event\EventRepositoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\Infrastructure\Entity\Subscriber\SubscriberIdFactory;
use Yiisoft\Inform\Infrastructure\Entity\Subscriber\SubscriberRepository;
use Yiisoft\Inform\SubDomain\GitHub\Domain\GithubRepositoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Entity\Event\EventIdFactory;
use Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Entity\Event\EventRepository;
use Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Entity\GithubRepository\GithubRepository;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Router;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Client\TelegramClientSymfony;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;
use Yiisoft\Yii\Queue\Adapter\AdapterInterface;
use Yiisoft\Yii\Queue\AMQP\Adapter;
use Yiisoft\Yii\Queue\AMQP\MessageSerializer;
use Yiisoft\Yii\Queue\AMQP\MessageSerializerInterface;
use Yiisoft\Yii\Queue\AMQP\Settings\Queue as QueueSettings;
use Yiisoft\Yii\Queue\Queue;
use Yiisoft\Yii\Queue\QueueInterface;

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
    ],
    EventIdFactoryInterface::class => EventIdFactory::class,
    EventRepositoryInterface::class => EventRepository::class,
    QueueInterface::class => Queue::class,
    AdapterInterface::class => Adapter::class,
    MessageSerializerInterface::class => MessageSerializer::class,
    AbstractConnection::class => AMQPLazyConnection::class,
    AMQPLazyConnection::class => [
        '__construct()' => [ // TODO move to params
            'host' => 'amqp',
            'port' => 5672,
            'user' => getenv('AMQP_USER'),
            'password' => getenv('AMQP_PASSWORD'),
            'keepalive' => true,
        ],
    ],
    QueueSettings::class => [
        '__construct()' => ['queueName' => 'yii-queue'],
    ],
];
