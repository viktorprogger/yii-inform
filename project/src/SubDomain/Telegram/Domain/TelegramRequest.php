<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain;

use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;

final class TelegramRequest
{
    public function __construct(
        public readonly string $chatId,
        public readonly string $request,
        public readonly Subscriber $subscriber,
        public readonly ?string $callbackQueryId = null,
    ) {
    }
}
