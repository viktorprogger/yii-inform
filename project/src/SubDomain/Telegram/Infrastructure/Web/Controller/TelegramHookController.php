<?php

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Web\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\Application;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

class TelegramHookController
{
    public function __construct(
        private readonly DataResponseFactoryInterface $responseFactory,
        private readonly Application $application,
    )
    {
    }

    public function hook(ServerRequestInterface $request)
    {
        $this->application->handle($request->getParsedBody());

        return $this->responseFactory->createResponse();
    }
}
