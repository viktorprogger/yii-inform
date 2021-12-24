<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\ButtonAction;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButton;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryKeyboard;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramMessageUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;

final class RealtimeAction implements ActionInterface
{
    public function __construct(
        private readonly RepositoryButtonRepository $buttonService,
        private readonly Formatter $formatter,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        preg_match("#^/realtime:(\d+)$#", $request->requestData, $matches, PREG_UNMATCHED_AS_NULL);
        $page = (int) ($matches[1] ?? 1);
        $isButtonPressed = $request->callbackQueryId !== null;
        $buttons = $this->buttonService->createKeyboard($request->subscriber, SubscriptionType::REALTIME);
        $pagination = (new OffsetPaginator(new IterableDataReader($buttons)))
            ->withPageSize(21)
            ->withCurrentPage($page);
        $text = "*Страница $page\n\n*" .
            "Используйте кнопки ниже, чтобы подписаться на обновления репозитория или отписаться от них:";

        $keyboard = $this->formatter->format(
            SubscriptionType::REALTIME,
            3,
            $pagination
        );

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
