<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

interface ActionInterface
{
    public function handle(TelegramRequest $request, Response $response): Response;
}
