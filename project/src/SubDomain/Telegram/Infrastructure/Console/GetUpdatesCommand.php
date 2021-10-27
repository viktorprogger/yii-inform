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
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SummaryAction;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Entity\TelegramUpdateEntity;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Entity\TgUpdateEntityCycleRepository;
use Yiisoft\Yii\Console\ExitCode;

final class GetUpdatesCommand extends Command
{
    protected static $defaultName = 'inform/updates';

    public function __construct(
        private TelegramClientInterface $client,
//        private Client $sentry,
        private SubscriberIdFactoryInterface $subscriberIdFactory,
        private SubscriberRepositoryInterface $subscriberRepository,
        private ContainerInterface $container,
        private ORM $orm,
        private TgUpdateEntityCycleRepository $tgUpdateEntityCycleRepository,
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
            dump($update);
            $response = new Response();

            if (isset($update['callback_query'])) {
                $response = $response->withCallbackResponse(new TelegramCallbackResponse($update['callback_query']['id']));
            }

            $message = $update['message'] ?? $update['callback_query'];
            $subscriberId = $this->subscriberIdFactory->create('tg-' . $message['from']['id']);
            $subscriber = $this->subscriberRepository->find($subscriberId);
            if ($subscriber === null) {
                $subscriber = new Subscriber($subscriberId, new Settings());
                $this->subscriberRepository->create($subscriber);
            }

            $data = trim($message['text'] ?? $message['data']);
            $chatId = (string) ($message['chat']['id'] ?? $message['message']['chat']['id']);
            $request = new TelegramRequest($chatId, $data, $subscriber);

            if (in_array(trim($data), ['/start'], true)) {
                $action = $this->container->get(HelloAction::class);
                $response = $action->handle($request, $response);
            }

            if (in_array(trim($data), ['/realtime'], true)) {
                $action = $this->container->get(RealtimeAction::class);
                $response = $action->handle($request, $response);
            }

            if (in_array(trim($data), ['/summary'], true)) {
                $action = $this->container->get(SummaryAction::class);
                $response = $action->handle($request, $response);
            }

            if (preg_match("/^realtime:[+-]:[\w_-]+$/", $data)) {
                $action = $this->container->get(RealtimeEditAction::class);
                $response = $action->handle($request, $response);
            }

            /*if (strpos($data, '/create_wallet ') === 0) {
                $walletName = trim(explode(' ', $data, 2)[1] ?? '');
                if ($walletName === '') {
                    // TODO send error message
                } else {
                    $action = $this->container->get(GetWalletsAction::class);
                    $response = $action->handle(new TelegramRequest($subscriberId, $chatId), $response);
                }
            }
            dump($response);*/


            foreach ($response->getCallbackQueries() as $callbackQuery) {
                $this->client->send(
                    'answerCallbackQuery',
                    [
                        'callback_query_id' => $callbackQuery->getId(),
                        'text' => $callbackQuery->getText(),
                        'show_alert' => $callbackQuery->isShowAlert(),
                        'url' => $callbackQuery->getUrl(),
                        'cache_time' => $callbackQuery->getCacheTime(),
                    ],
                );
            }

            $updateEntity = new TelegramUpdateEntity();
            $updateEntity->contents = json_encode($update);
            $updateEntity->created_at = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
            $updateEntity->id = $update['update_id'];

            (new Transaction($this->orm))->persist($updateEntity)->run();
            foreach ($response->getMessages() as $message) {
                $this->client->sendMessage($message);
            }
        }

        return ExitCode::OK;
    }
}
