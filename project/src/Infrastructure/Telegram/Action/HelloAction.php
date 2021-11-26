<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Telegram\Action;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

final class HelloAction implements ActionInterface
{
    public function handle(TelegramRequest $request, Response $response): Response
    {
        $text = <<<'TXT'
            Добро пожаловать\! Этот бот позволит вам отслеживать обновления Yii3 прямо в Telegram\.
            Доступные команды:
            \- /realtime \- настройка получения обновлений из репозиториев в реальном времени
            \- /summary \- настройка периодического получения обновлений \(раз в сутки\)\.
            TXT;


        return $response->withMessage(
            new TelegramMessage(
                $text,
                MessageFormat::markdown(),
                $request->chatId,
            ),
        );
    }
}
