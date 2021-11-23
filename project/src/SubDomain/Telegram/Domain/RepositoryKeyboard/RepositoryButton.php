<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\RepositoryKeyboard;

final class RepositoryButton
{
    public function __construct(
        public readonly string $name,
        public readonly ButtonAction $action,
    )
    {
    }
}
