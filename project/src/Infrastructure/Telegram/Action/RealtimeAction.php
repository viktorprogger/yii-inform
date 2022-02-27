<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\TelegramBot\Domain\Client\InlineKeyboardButton;
use Viktorprogger\TelegramBot\Domain\Client\MessageFormat;
use Viktorprogger\TelegramBot\Domain\Client\Response;
use Viktorprogger\TelegramBot\Domain\Client\ResponseInterface;
use Viktorprogger\TelegramBot\Domain\Client\TelegramMessage;
use Viktorprogger\TelegramBot\Domain\Client\TelegramMessageUpdate;
use Viktorprogger\TelegramBot\Domain\UpdateRuntime\RequestHandlerInterface;
use Viktorprogger\TelegramBot\Domain\Entity\Request\TelegramRequest;
use Viktorprogger\YiisoftInform\Domain\SubscriptionType;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\Middleware\SubscriberMiddleware;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;

final class RealtimeAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly RepositoryButtonRepository $buttonService,
        private readonly Formatter $formatter,
    ) {
    }

    public function handle(TelegramRequest $request): ResponseInterface
    {
        preg_match("#^/realtime:(\d+)$#", $request->requestData, $matches, PREG_UNMATCHED_AS_NULL);
        $page = (int) ($matches[1] ?? 1);
        $isButtonPressed = $request->callbackQueryId !== null;
        $buttons = $this->buttonService->createKeyboard($request->getAttribute(SubscriberMiddleware::ATTRIBUTE), SubscriptionType::REALTIME);
        $pagination = (new OffsetPaginator(new IterableDataReader($buttons)))
            ->withPageSize(21)
            ->withCurrentPage($page);
        $text = <<<TXT
            *Настройка подписки на обновления в реальном времени*
            **Страница $page**

            Используйте кнопки ниже, чтобы подписаться на обновления репозитория или отписаться от них:
            TXT;

        $keyboard = $this->formatter->format(
            SubscriptionType::REALTIME,
            3,
            $pagination
        );

        $keyboard[] = [new InlineKeyboardButton('< В меню', '/start')];
        $response = new Response();
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
