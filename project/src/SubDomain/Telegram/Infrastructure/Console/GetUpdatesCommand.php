<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Console;

use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use DateTimeImmutable;
use DateTimeZone;
use Github\Client;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\Settings;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\HelloAction;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\RealtimeAction;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\RealtimeEditAction;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SummaryAction;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequestFactory;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Entity\TelegramUpdateEntity;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Entity\TgUpdateEntityCycleRepository;
use Yiisoft\Yii\Console\ExitCode;

final class GetUpdatesCommand extends Command
{
    protected static $defaultName = 'inform/updates';

    public function __construct(
        private TelegramClientInterface $client,
//        private Client $sentry,
        private ContainerInterface $container,
        private ORM $orm,
        private TgUpdateEntityCycleRepository $tgUpdateEntityCycleRepository,

        private readonly TelegramRequestFactory $telegramRequestFactory,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TelegramUpdateEntity|null $lastUpdate */
        $lastUpdate = $this->tgUpdateEntityCycleRepository
            ->select()
            ->orderBy('id', 'DESC')
            ->fetchOne();

        $data = ['allowed_updates' => ['message', 'callback_query']];
        if ($lastUpdate !== null) {
            $data['offset'] = $lastUpdate->id + 1;
        }
        foreach ($this->client->send('getUpdates', $data)['result'] ?? [] as $update) {
            // dump($update);
            $request = $this->telegramRequestFactory->create($update);
            $response = new Response();

            // TODO routing
            if ($request->requestData === '/start') {
                $action = $this->container->get(HelloAction::class);
                $response = $action->handle($request, $response);
            }

            if ($request->requestData === '/realtime') {
                $action = $this->container->get(RealtimeAction::class);
                $response = $action->handle($request, $response);
            }

            if ($request->requestData === '/summary') {
                $action = $this->container->get(SummaryAction::class);
                $response = $action->handle($request, $response);
            }

            $type = SubscriptionType::REALTIME->value;
            if (preg_match("/^$type:[+-]:[\w_-]+$/", $request->requestData)) {
                $action = $this->container->get(RealtimeEditAction::class);
                $response = $action->handle($request, $response);
            }

            $type = SubscriptionType::SUMMARY->value;
            if (preg_match("/^$type:[+-]:[\w_-]+$/", $request->requestData)) {
                $action = $this->container->get(RealtimeEditAction::class);
                $response = $action->handle($request, $response);
            }

            if ($request->callbackQueryId !== null) {
                $callbackResponse = $response->getCallbackResponse() ?? new TelegramCallbackResponse($request->callbackQueryId);
                $this->client->send(
                    'answerCallbackQuery',
                    [
                        'callback_query_id' => $callbackResponse->getId(),
                        'text' => $callbackResponse->getText(),
                        'show_alert' => $callbackResponse->isShowAlert(),
                        'url' => $callbackResponse->getUrl(),
                        'cache_time' => $callbackResponse->getCacheTime(),
                    ],
                );
            }

            foreach ($response->getKeyboardUpdates() as $message) {
                $this->client->updateKeyboard($message);
            }

            foreach ($response->getMessages() as $message) {
                $this->client->sendMessage($message);
            }

            $updateEntity = new TelegramUpdateEntity();
            $updateEntity->contents = json_encode($update);
            $updateEntity->created_at = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
            $updateEntity->id = $update['update_id'];
            (new Transaction($this->orm))->persist($updateEntity)->run();
        }

        return ExitCode::OK;
    }
}
