<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramRequestException;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TooManyRequestsException;

final class TelegramClientSymfony implements TelegramClientInterface
{
    private const URI = 'https://api.telegram.org/';
    private const ERRORS_IGNORED = [
        'Bad Request: query is too old and response timeout expired or query ID is invalid',
        'Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message',
    ];

    public function __construct(private string $token, private HttpClientInterface $client)
    {
    }

    public function sendMessage(TelegramMessage $message): ?array
    {
        return $this->send('sendMessage', $message->getArray());
    }

    public function updateMessage(mixed $message): ?array
    {
        return $this->send('editMessageText', $message->getArray());
    }

    public function updateKeyboard(TelegramKeyboardUpdate $message): ?array
    {
        return $this->send('editMessageReplyMarkup', $message->getArray());
    }

    public function send(string $apiEndpoint, array $data = []): ?array
    {
        try {
            $response = $this->client->request(
                'POST',
                self::URI . "bot$this->token/$apiEndpoint",
                ['json' => $data]
            )->getContent();
        } catch (ClientExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                throw new TooManyRequestsException($e->getMessage(), previous: $e);
            }

            throw new TelegramRequestException($e->getMessage(), previous: $e);
        }

        if (!empty($response)) {
            $response = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
            if ($response['ok'] === false && !in_array($response['description'], self::ERRORS_IGNORED, true)) {
                throw new RuntimeException($response['description']);
            }

            return $response;
        }

        return null;
    }
}
