<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\UpdateRuntime;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

final class Emitter
{
    public function __construct(private readonly TelegramClientInterface $client)
    {
    }

    public function emit(Response $response, ?string $callbackQueryId): void
    {
        if ($callbackQueryId !== null) {
            $callbackResponse = $response->getCallbackResponse() ?? new TelegramCallbackResponse($callbackQueryId);
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
    }
}
