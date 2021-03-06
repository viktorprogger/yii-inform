<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Console;

use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventType;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\GithubService;
use Yiisoft\Yii\Console\ExitCode;

final class LoadRepositoriesCommand extends Command
{
    protected static $defaultName = 'inform/github/load-repos';

    public function __construct(
        private readonly GithubService $service,
        private readonly GithubRepositoryInterface $repository,
        private readonly EventIdFactoryInterface $eventIdFactory,
        private readonly EventRepositoryInterface $eventRepository,
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
            foreach ($diff as $repo) {
                $event = new GithubEvent(
                    $this->eventIdFactory->create(),
                    EventType::NEW_REPO,
                    $repo,
                    [],
                    new DateTimeImmutable(),
                );
                $this->eventRepository->create($event);
            }
        }

        return ExitCode::OK;
    }
}
