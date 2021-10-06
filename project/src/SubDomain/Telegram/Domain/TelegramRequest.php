<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain;

final class TelegramRequest
{
    public function __construct(public readonly string $chatId, public readonly string $request)
    {
    }
}
