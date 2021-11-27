<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramClientInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\Application;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Entity\TelegramUpdateEntity;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Entity\TgUpdateEntityCycleRepository;
use Yiisoft\Yii\Console\ExitCode;

final class GetUpdatesCommand extends Command
{
    protected static $defaultName = 'inform/tg/updates';

    public function __construct(
        private readonly TgUpdateEntityCycleRepository $tgUpdateEntityCycleRepository,
        private readonly TelegramClientInterface $client,
        private readonly Application $application,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TelegramUpdateEntity|null $lastUpdate */
        $lastUpdate = $this->tgUpdateEntityCycleRepository
            ->select()
            ->orderBy('id', 'DESC')
            ->fetchOne();

        $data = ['allowed_updates' => ['message', 'callback_query']];
        if ($lastUpdate !== null) {
            $data['offset'] = $lastUpdate->id + 1;
        }

        foreach ($this->client->send('getUpdates', $data)['result'] ?? [] as $update) {
            $this->application->handle($update);
        }

        return ExitCode::OK;
    }
}
