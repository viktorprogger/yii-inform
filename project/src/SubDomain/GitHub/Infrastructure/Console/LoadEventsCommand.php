<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\GithubService;
use Yiisoft\Yii\Console\ExitCode;

class LoadEventsCommand extends Command
{
    protected static $defaultName = 'inform/github/load-events';
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