<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain;

use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use DateTimeImmutable;
use DateTimeZone;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Entity\TelegramUpdateEntity;

final class Application
{
    public function __construct(
//        private Client $sentry,
        private readonly ORM $orm,
        private readonly TelegramRequestFactory $telegramRequestFactory,
        private readonly Router $router,
        private readonly Emitter $emitter,
    ) {
    }

    /**
     * @param array $update An update entry got from Telegram
     *
     * @return void
     * @see https://core.telegram.org/bots/api#update
     *
     */
    public function handle(array $update): void
    {
        // dump($update);
        $request = $this->telegramRequestFactory->create($update);
        $response = $this->router->match($request)->handle($request, new Response());
        $this->emitter->emit($response, $request->callbackQueryId);

        $updateEntity = new TelegramUpdateEntity();
        $updateEntity->contents = json_encode($update, JSON_THROW_ON_ERROR);
        $updateEntity->created_at = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
        $updateEntity->id = $update['update_id'];
        (new Transaction($this->orm))->persist($updateEntity)->run();
    }
}
