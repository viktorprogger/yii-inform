<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Telegram\Action;

use Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

class SummaryAction implements ActionInterface
{
    public function __construct(
        private readonly RepositoryButtonRepository $buttonService,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        $text = 'Вы можете подписаться на следующие репозитории:';

        $message = new TelegramMessage(
            $text,
            MessageFormat::markdown(),
            $request->chatId,
            $this->buttonService->createKeyboard($request->subscriber, SubscriptionType::SUMMARY),
        );

        return $response->withMessage($message);
    }
}
