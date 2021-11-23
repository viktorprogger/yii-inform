<?php

namespace Yiisoft\Inform\Infrastructure\Entity\GithubRepository;

use Cycle\ORM\ORM;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Transaction;

class GithubRepository implements \Yiisoft\Inform\Domain\GithubRepository\GithubRepositoryInterface
{
    private readonly Repository $repo;

    public function __construct(private readonly ORM $orm)
    {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->repo = $orm->getRepository(GithubRepositoryEntity::class);
    }

    public function all(): array
    {
        $result = $this->repo->select()->orderBy('name')->limit(150)->fetchAll();

        return array_map(static fn (GithubRepositoryEntity $record) => $record->name, $result);
    }

    public function add(string ...$repositories): void
    {
        $transaction = new Transaction($this->orm);
        foreach ($repositories as $repository) {
            $transaction->persist(new GithubRepositoryEntity($repository));
        }

        $transaction->run();
    }

    public function delete(string ...$repositories): void
    {
        $transaction = new Transaction($this->orm);
        foreach ($repositories as $repository) {
            $transaction->delete(new GithubRepositoryEntity($repository));
        }

        $transaction->run();
    }
}
