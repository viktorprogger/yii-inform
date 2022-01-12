<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;

final class SetTelegramWebhookCommand extends Command
{
    protected static $defaultName = 'inform/tg/set-webhook';
    protected static $defaultDescription = 'Set TG webhook address';

    public function __construct(private TelegramClientInterface $client, string $name = null)
    {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fields = [
            'url' => 'https://' .getenv('DOMAIN') . (getenv('URL_PREFIX') ?: '') . '/telegram/hook',
            'allowed_updates' => ['message', 'callback_query'],
        ];

        $this->client->send('setWebhook', $fields);
    }
}
