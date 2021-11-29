<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime;

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

final class Emitter
{
    public function __construct(private readonly TelegramClientInterface $client)
    {
    }

    public function emit(Response $response, ?string $callbackQueryId): void
    {
        if ($callbackQueryId !== null) {
            $callbackResponse = $response->getCallbackResponse() ?? new TelegramCallbackResponse($callbackQueryId);
            $data = [
                'callback_query_id' => $callbackResponse->getId(),
                'text' => $callbackResponse->getText(),
                'show_alert' => $callbackResponse->isShowAlert(),
                'cache_time' => $callbackResponse->getCacheTime(),
            ];

            $url = $callbackResponse->getUrl();
            if ($url !== null) {
                $data['url'] = $url;
            }
            $this->client->send(
                'answerCallbackQuery',
                $data,
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
