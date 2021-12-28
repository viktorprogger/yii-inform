<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client;

interface TelegramClientInterface
{
    /**
     * @throws TelegramRequestException
     * @throws TooManyRequestsException
     */
    public function sendMessage(TelegramMessage $message): ?array;

    /**
     * @throws TelegramRequestException
     * @throws TooManyRequestsException
     */
    public function updateKeyboard(TelegramKeyboardUpdate $message): ?array;

    /**
     * @throws TelegramRequestException
     * @throws TooManyRequestsException
     */
    public function send(string $apiEndpoint, array $data = []): ?array;

    /**
     * @throws TelegramRequestException
     * @throws TooManyRequestsException
     */
    public function updateMessage(mixed $message): ?array;
}
