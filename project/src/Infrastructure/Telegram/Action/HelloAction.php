<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessageUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

final class HelloAction implements ActionInterface
{
    public function handle(TelegramRequest $request, Response $response): Response
    {
        $isButtonPressed = $request->callbackQueryId !== null;

        $text = <<<'TXT'
            Добро пожаловать\! Этот бот позволит вам отслеживать обновления репозиториев Yii3 прямо в Telegram\.
            Доступные команды:
            \- **realtime** \- настройка получения обновлений из репозиториев в реальном времени
            \- **summary** \- настройка периодического получения обновлений \(раз в сутки\)\.
            TXT;
        $keyboard = [
            [
                new InlineKeyboardButton('Realtime', '/realtime'),
                new InlineKeyboardButton('Summary', '/summary'),
            ],
        ];

        if ($isButtonPressed) {
            $message = new TelegramMessageUpdate(
                $text,
                MessageFormat::markdown(),
                $request->chatId,
                $request->messageId,
                $keyboard,
            );

            $response = $response->withMessageUpdate($message);
        } else {
            $message = new TelegramMessage(
                $text,
                MessageFormat::markdown(),
                $request->chatId,
                $keyboard,
            );
            $response = $response->withMessage($message);
        }

        return $response;
    }
}
