<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

final class SetTelegramWebhookCommand extends Command
{
    public function __construct(private TelegramClientInterface $client, private string $botToken, string $name = null)
    {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fields = [
            'url' => 'https://wallet.viktorprogger.com/wallet/telegram-webhook',
            'allowed_updates' => ['message'],
        ];

        $this->client->send('setWebhook', $fields);
    }
}
