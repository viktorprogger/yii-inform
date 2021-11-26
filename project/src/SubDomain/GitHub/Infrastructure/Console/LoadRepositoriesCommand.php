<?php

namespace Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Yiisoft\Inform\SubDomain\GitHub\Domain\GithubService;
use Yiisoft\Yii\Console\ExitCode;

class LoadRepositoriesCommand extends Command
{
    protected static $defaultName = 'inform/github/load-repos';

    public function __construct(
        private readonly GithubService $service,
        private readonly GithubRepositoryInterface $repository,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repositoriesActual = $this->service->loadYii3Packages();
        $repositoriesSaved = $this->repository->all();
        if ($repositoriesSaved === []) {
            $this->repository->add(...$repositoriesActual);

            return ExitCode::OK;
        }

        $diff = array_diff($repositoriesSaved, $repositoriesActual);
        if ($diff !== []) {
            $this->repository->delete(...$diff);
        }

        $diff = array_diff($repositoriesActual, $repositoriesSaved);
        if ($diff !== []) {
            $this->repository->add(...$diff);
            // TODO add event; Messages will be sent to users
        }

        return ExitCode::OK;
    }
}
