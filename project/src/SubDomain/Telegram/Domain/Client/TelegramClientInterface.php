<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Client;

interface TelegramClientInterface
{
    public function sendMessage(TelegramMessage $message): ?array;

    public function updateKeyboard(TelegramKeyboardUpdate $message): ?array;

    public function send(string $apiEndpoint, array $data = []): ?array;
}
