<?php

declare(strict_types=1);

use Github\AuthMethod;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
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
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber\SubscriberIdFactory;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber\SubscriberRepository;
use Viktorprogger\YiisoftInform\Infrastructure\RequestIdLogProcessor;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\GithubService;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Client\Client as GithubClient;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\Event\EventIdFactory;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\Event\EventRepository;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\GithubRepository\GithubRepository;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\Router;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client\TelegramClientLog;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client\TelegramClientSymfony;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\Apcu\ApcuCache;
use Yiisoft\Definitions\Reference;
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
    TelegramClientInterface::class => TelegramClientLog::class,
    TelegramClientSymfony::class => [
        '__construct()' => [
            'token' => getenv('BOT_TOKEN'),
            'logger' => Reference::to('loggerTelegram'),
        ],
    ],
    TelegramClientLog::class => [
        '__construct()' => [
            'logger' => Reference::to('loggerTelegram'),
        ],
    ],
    HttpClientInterface::class => static fn() => HttpClient::create(),
    SubscriberIdFactoryInterface::class => SubscriberIdFactory::class,
    SubscriberRepositoryInterface::class => SubscriberRepository::class,
    UuidFactoryInterface::class => UuidFactory::class,
    LoggerInterface::class => Logger::class,
    Logger::class => static function(Aliases $alias, RequestIdLogProcessor $requestIdLogProcessor) {
        return (new Logger('application'))
            ->pushProcessor(static function (array $record): array {
                if (isset($record['extra'])) {
                    $record['context']['extra'] = $record['extra'] ?? [];
                    unset($record['extra']);
                }

                return $record;
            })
            ->pushProcessor(new PsrLogMessageProcessor())
            ->pushProcessor(new MemoryUsageProcessor())
            ->pushProcessor(new MemoryPeakUsageProcessor())
            ->pushProcessor(new IntrospectionProcessor())
            ->pushProcessor($requestIdLogProcessor)
            ->pushHandler(
                (new RotatingFileHandler($alias->get('@runtime/logs/app.log')))
                    ->setFilenameFormat('{filename}-{date}', RotatingFileHandler::FILE_PER_MONTH)
                    ->setFormatter(new JsonFormatter())
            );
    },
    'loggerTelegram' => static fn(Logger $logger) => $logger->withName('telegram'),
    'loggerGithub' => static fn(Logger $logger) => $logger->withName('github'),
    'loggerCycle' => static fn(Logger $logger) => $logger->withName('cycle'),
    GithubService::class => [
        '__construct()' => ['logger' => Reference::to('loggerGithub')]
    ],
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
        //TODO move to params
        $options = ['dsn' => getenv('SENTRY_DSN'), 'environment' => getenv('YII_ENV')];
        $client = ClientBuilder::create($options)->getClient();
        $hub = SentrySdk::init();
        $hub->bindClient($client);

        return $hub;
    },
];
