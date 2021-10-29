<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Inform\Domain\GithubRepository\GithubRepositoryInterface;
use Yiisoft\Inform\Domain\GithubService;
use Yiisoft\Yii\Console\ExitCode;

class LoadEventsCommand extends Command
{
    protected static $defaultName = 'load-events';
    protected static $defaultDescription = 'Loading events from GitHub';

    public function __construct(
        private readonly GithubService $service,
        private readonly GithubRepositoryInterface $repository,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->loadEvents(...$this->repository->all());

        return ExitCode::OK;
    }
}
