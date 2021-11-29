<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;

final class TelegramClientSymfony implements TelegramClientInterface
{
    private const URI = 'https://api.telegram.org/';

    public function __construct(private string $token, private HttpClientInterface $client)
    {
    }

    public function sendMessage(TelegramMessage $message): ?array
    {
        return $this->send('sendMessage', $message->getArray());
    }

    public function updateKeyboard(TelegramKeyboardUpdate $message): ?array
    {
        return $this->send('editMessageReplyMarkup', $message->getArray());
    }

    public function send(string $apiEndpoint, array $data = []): ?array
    {
        // try {
            $response = $this->client->request(
                'POST',
                self::URI . "bot$this->token/$apiEndpoint",
                ['json' => $data]
            )->getContent(false);

            if (!empty($response)) {
                $response = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
                if ($response['ok'] === false && $response['description'] !== 'Bad Request: query is too old and response timeout expired or query ID is invalid') {
                    throw new RuntimeException($response['description']);
                }

                return $response;
            }
        /*} catch (ClientExceptionInterface) {
            // TODO
        }*/

        return null;
    }
}
