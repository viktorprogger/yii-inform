<?php

namespace Yiisoft\Inform\Domain;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

class RealtimeEventSender
{
    public function __construct(
        private readonly TelegramMessageGenerator $generator,
        private readonly TelegramClientInterface $client,
    ) {
    }

    public function send()
    {

    }
}
