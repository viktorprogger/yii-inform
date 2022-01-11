<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client;

use Psr\Log\LoggerInterface;
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

    public function __construct(
        private readonly string $token,
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
    )
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
        $this->logger->info('Sending Telegram request', ['endpoint' => $apiEndpoint, 'data' => $data]);

        try {
            $response = $this->client->request(
                'POST',
                self::URI . "bot$this->token/$apiEndpoint",
                ['json' => $data]
            );
            $responseContent = $response->getContent();
        } catch (ClientExceptionInterface $e) {
            $response = $e->getResponse()->getContent(false);
            $this->logger->info(
                'Telegram request error',
                [
                    'endpoint' => $apiEndpoint,
                    'data' => $data,
                    'responseRaw' => $response,
                    'response' => json_decode($response, true),
                    'responseCode' => $e->getResponse()->getStatusCode(),
                    'error' => $e->getMessage(),
                ]
            );

            if ($e->getResponse()->getStatusCode() === 429) {
                throw new TooManyRequestsException($e->getMessage(), previous: $e);
            }

            throw new TelegramRequestException($e->getMessage(), previous: $e);
        }

        if (!empty($responseContent)) {
            $decoded = json_decode($responseContent, true, flags: JSON_THROW_ON_ERROR);
            $context = [
                'endpoint' => $apiEndpoint,
                'data' => $data,
                'responseRaw' => $responseContent,
                'response' => $decoded,
                'responseCode' => $response->getStatusCode(),
            ];

            if (($decoded['ok'] ?? false) === false) {
                $context['error'] = $decoded['description'] ?? '';
                if (in_array($decoded['description'] ?? '', self::ERRORS_IGNORED, true)) {
                    $this->logger->warning(
                        'Error occurred while sending Telegram request',
                        $context
                    );
                } else {
                    $this->logger->error(
                        'Error occurred while sending Telegram request',
                        $context
                    );

                    throw new RuntimeException($decoded['description']);
                }
            }

            return $decoded;
        }

        return null;
    }
}
