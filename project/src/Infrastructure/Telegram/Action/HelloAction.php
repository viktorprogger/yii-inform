<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\TelegramBot\Domain\Client\InlineKeyboardButton;
use Viktorprogger\TelegramBot\Domain\Client\MessageFormat;
use Viktorprogger\TelegramBot\Domain\Client\Response;
use Viktorprogger\TelegramBot\Domain\Client\ResponseInterface;
use Viktorprogger\TelegramBot\Domain\Client\TelegramMessage;
use Viktorprogger\TelegramBot\Domain\Client\TelegramMessageUpdate;
use Viktorprogger\TelegramBot\Domain\Entity\Request\TelegramRequest;
use Viktorprogger\TelegramBot\Domain\UpdateRuntime\RequestHandlerInterface;

final class HelloAction implements RequestHandlerInterface
{
    public function handle(TelegramRequest $request): ResponseInterface
    {
        $response = new Response();
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
                MessageFormat::MARKDOWN,
                $request->chatId,
                $request->messageId,
                $keyboard,
            );

            $response = $response->withMessageUpdate($message);
        } else {
            $message = new TelegramMessage(
                $text,
                MessageFormat::MARKDOWN,
                $request->chatId,
                $keyboard,
            );
            $response = $response->withMessage($message);
        }

        return $response;
    }
}
