<?php

namespace Yiisoft\Inform\Domain\GithubRepository;

interface GithubRepositoryInterface
{
    public function all(): array;

    public function add(string ...$repositories): void;

    public function delete(string ...$repositories): void;
}
