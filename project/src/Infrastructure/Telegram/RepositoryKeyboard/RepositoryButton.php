<?php

namespace Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard;

final class RepositoryButton
{
    public function __construct(
        public readonly string $name,
        public readonly ButtonAction $action,
    )
    {
    }
}
