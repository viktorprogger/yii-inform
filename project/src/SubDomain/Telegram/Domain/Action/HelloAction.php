<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

class HelloAction implements ActionInterface
{
    public function handle(TelegramRequest $request, Response $response): Response
    {
        $text = 'Добро пожаловать\! Нажмите /realtime для настройки получения обновлений из репозиториев ' .
            'в реальном времени или /summary для настройки периодического получения обновлений';

        return $response->withMessage(
            new TelegramMessage(
                $text,
                MessageFormat::markdown(),
                $request->chatId,
            ),
        );
    }
}
