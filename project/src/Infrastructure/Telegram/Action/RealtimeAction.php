<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Telegram\Action;

use Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard\ButtonAction;
use Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButton;
use Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryKeyboard;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

final class RealtimeAction implements ActionInterface
{
    public function __construct(
        private readonly RepositoryButtonRepository $buttonService,
        private readonly Formatter $formatter,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        $text = 'Вы можете подписаться на следующие репозитории:';

        $keyboard = $this->buttonService->createKeyboard($request->subscriber, SubscriptionType::REALTIME);
        foreach ($keyboard->iterateBunch(100) as $key => $subKeyboard) {
            $key++;
            $message = new TelegramMessage(
                "$text\n\n*Часть $key*",
                MessageFormat::markdown(),
                $request->chatId,
                $this->formatter->format($subKeyboard, SubscriptionType::REALTIME),
            );

            $response = $response->withMessage($message);
        }

        return $response;
    }
}
