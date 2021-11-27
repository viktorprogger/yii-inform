<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action;

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

interface ActionInterface
{
    public function handle(TelegramRequest $request, Response $response): Response;
}
