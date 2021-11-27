<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Client;

use Psr\Log\LoggerInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;

final class TelegramClientLog implements TelegramClientInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function sendMessage(TelegramMessage $message): ?array
    {
        $this->send('sendMessage', $message->getArray());

        return null;
    }

    public function updateKeyboard(TelegramKeyboardUpdate $message): ?array
    {
        $this->send('sendMessage', $message->getArray());

        return null;
    }

    public function send(string $apiEndpoint, array $data = []): ?array
    {
        $fields = [
            'endpoint' => $apiEndpoint,
            'data' => $data,
        ];
        $this->logger->debug('A message to Telegram', $fields);

        return null;
    }
}
