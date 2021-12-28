<?php

declare(strict_types=1);

use Github\AuthMethod;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Sentry\ClientBuilder;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Client\Client as GithubClient;
use Yiisoft\Cache\Apcu\ApcuCache;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber\SubscriberIdFactory;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber\SubscriberRepository;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\Event\EventIdFactory;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\Event\EventRepository;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\GithubRepository\GithubRepository;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\Router;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client\TelegramClientSymfony;
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
    GithubClient::class => [
        'authenticate()' => [
            'tokenOrLogin' => getenv('GITHUB_TOKEN'),
            'authMethod' => AuthMethod::ACCESS_TOKEN,
        ],
    ],
    TelegramClientInterface::class => TelegramClientSymfony::class,
    TelegramClientSymfony::class => ['__construct()' => ['token' => getenv('BOT_TOKEN')]],
    HttpClientInterface::class => static fn() => HttpClient::create(),
    SubscriberIdFactoryInterface::class => SubscriberIdFactory::class,
    SubscriberRepositoryInterface::class => SubscriberRepository::class,
    UuidFactoryInterface::class => UuidFactory::class,
    LoggerInterface::class => Logger::class,
    Logger::class => static fn(FileTarget $target) => (new Logger([$target]))->setFlushInterval(1),
    CacheInterface::class => ApcuCache::class,
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
    HubInterface::class => static function(): HubInterface {
        $client = ClientBuilder::create(['dsn' => getenv('SENTRY_DSN')])->getClient(); //FIXME
        $hub = SentrySdk::init();
        $hub->bindClient($client);

        return $hub;
    },
];
