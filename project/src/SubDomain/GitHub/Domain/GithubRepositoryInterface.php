<?php

namespace Yiisoft\Inform\SubDomain\GitHub\Domain;

interface GithubRepositoryInterface
{
    public function all(): array;

    public function add(string ...$repositories): void;

    public function delete(string ...$repositories): void;
}
