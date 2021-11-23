<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\RepositoryKeyboard\ButtonAction;
use Yiisoft\Inform\SubDomain\Telegram\Domain\RepositoryKeyboard\RepositoryButton;
use Yiisoft\Inform\SubDomain\Telegram\Domain\RepositoryKeyboard\RepositoryButtonRepository;
use Yiisoft\Inform\SubDomain\Telegram\Domain\RepositoryKeyboard\RepositoryKeyboard;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

final class RealtimeAction implements ActionInterface
{
    public function __construct(
        private readonly RepositoryButtonRepository $buttonService,
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
                $this->formatKeyboard($subKeyboard),
            );

            $response = $response->withMessage($message);
        }

        // TODO проверить. А то отрефакторил наугад)
        return $response;
    }

    private function formatKeyboard(RepositoryKeyboard $subKeyboard): array
    {
        $result = [];
        $perLine = 3;
        $count = 0;
        $line = 0;

        /** @var RepositoryButton $button */
        foreach ($subKeyboard as $button) {
            if ($count !== 0 && $count % $perLine === 0) {
                $line++;
            }
            $count++;

            if ($button->action === ButtonAction::REMOVE) {
                $emoji = '➖';
                $sign = '-';
            } else {
                $emoji = '➕';
                $sign = '+';
            }

            $type = SubscriptionType::REALTIME->value;
            $text = "$emoji $button->name";
            $callbackData = "$type:$sign:$button->name";

            $result[$line][] = new InlineKeyboardButton($text, $callbackData);
        }

        return $result;
    }
}
