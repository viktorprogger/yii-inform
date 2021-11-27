<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity;

interface GithubRepositoryInterface
{
    public function all(): array;

    public function add(string ...$repositories): void;

    public function delete(string ...$repositories): void;
}
