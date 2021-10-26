<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Github\Client;
use Symfony\Component\VarDumper\VarDumper;
use Yiisoft\Inform\Domain\GithubRepository;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

final class RealtimeAction implements ActionInterface
{
    public function __construct(
        private GithubRepository $repos,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        $keyboard = [];
        $perLine = 3;
        $count = 0;
        $line = 0;

        foreach ($this->repos->getYii3Packages() as $repository) {
            // TODO Нужен чекбокс или крестик для опознавания: удалить или добавить
            // TODO То же самое нужно и в callbackData
            if ($count !== 0 && $count % $perLine === 0) {
                $line++;
            }
            $count++;

            if (in_array($repository, $request->subscriber->settings->realtimeRepositories, true)) {
                $emoji = '➖';
                $sign = '-';
            } else {
                $emoji = '➕';
                $sign = '+';
            }

            $keyboard[$line][] = new InlineKeyboardButton("$emoji $repository", "/realtime:$sign:$repository");
        }

        $text = 'Вы можете подписаться на следующие репозитории:';

        return $response->withMessage(new TelegramMessage($text, MessageFormat::markdown(), $request->chatId, $keyboard));
    }
}
