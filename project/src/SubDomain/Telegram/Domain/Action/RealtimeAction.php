<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Github\Client;
use Symfony\Component\VarDumper\VarDumper;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\MessageFormat;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramMessage;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

class RealtimeAction implements ActionInterface
{
    public function __construct(
        private Client $github,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        // TODO их надо сортировать по имени
        $keyboard = [];
        $page = 1;
        $perLine = 3;
        $count = 1;
        $line = 0;
        do {
            $repositories = $this->github->repositories()->org('yiisoft', ['page' => $page++, 'per_page' => 200]);
            foreach ($repositories as $repository) {
                if (in_array('yii3', $repository['topics'], true)) {
                    // TODO Нужен чекбокс или крестик для опознавания: удалить или добавить
                    // TODO То же самое нужно и в callbackData
                    if ($count % $perLine === 0) {
                        $count++;
                        $line++;
                    }
                    $keyboard[$line][] = new InlineKeyboardButton($repository['name'], "/realtime:{$repository['name']}");
                }
            }
        } while ($repositories !== []);

        $text = 'Вы можете подписаться на следующие репозитории:';

        return $response->withMessage(new TelegramMessage($text, MessageFormat::markdown(), $request->chatId, $keyboard));
    }
}
