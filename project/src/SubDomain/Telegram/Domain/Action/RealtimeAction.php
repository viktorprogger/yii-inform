<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

final class RealtimeAction implements ActionInterface
{
    public function __construct(
        private readonly RepositoryButtonService $buttonService,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        $text = 'Вы можете подписаться на следующие репозитории:';

        $message = new TelegramMessage(
            $text,
            MessageFormat::markdown(),
            $request->chatId,
            $this->buttonService->createKeyboard($request->subscriber),
        );

        return $response->withMessage($message);
    }
}
