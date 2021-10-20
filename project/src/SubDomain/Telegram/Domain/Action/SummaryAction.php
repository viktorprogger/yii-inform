<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Github\Client;
use Symfony\Component\VarDumper\VarDumper;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

class SummaryAction implements ActionInterface
{
    public function __construct(
        private Client $github,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        foreach ($this->github->repositories()->org('yiisoft') as $repoInfo) {
            VarDumper::dump($repoInfo);
        }
    }
}
