<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard;

use Viktorprogger\TelegramBot\Domain\Client\InlineKeyboardButton;
use Viktorprogger\YiisoftInform\Domain\SubscriptionType;
use Yiisoft\Data\Paginator\OffsetPaginator;

final class Formatter
{
    public const REPO_ALL = 'all-repos';

    public function format(SubscriptionType $type, int $perLine, OffsetPaginator $pagination): array
    {
        $result = [];
        $count = 0;
        $line = 0;

        $result[$line][] = $this->createInlineButton(
            self::REPO_ALL,
            ButtonAction::ADD,
            $type,
            $pagination->getCurrentPage(),
            'Подписаться на все'
        );
        $result[$line][] = $this->createInlineButton(
            self::REPO_ALL,
            ButtonAction::REMOVE,
            $type,
            $pagination->getCurrentPage(),
            'Отписаться ото всего'
        );
        $line++;

        /** @var RepositoryButton $button */
        foreach ($pagination->read() as $button) {
            if ($count !== 0 && $count % $perLine === 0) {
                $line++;
            }
            $count++;

            $result[$line][] = $this->createInlineButton($button->name, $button->action, $type, $pagination->getCurrentPage());
        }

        $this->addPagination($result, $pagination, $type);

        return $result;
    }

    public function createInlineButton(
        string $repository,
        ButtonAction $action,
        SubscriptionType $type,
        int $page,
        ?string $label = null,
    ): InlineKeyboardButton {
        if ($action === ButtonAction::REMOVE) {
            $emoji = '➖';
            $sign = '-';
        } else {
            $emoji = '➕';
            $sign = '+';
        }

        $text = $label ?? "$emoji $repository";
        $callbackData = "$type->value:$sign:$repository:$page";

        return new InlineKeyboardButton($text, $callbackData);
    }

    private function addPagination(array &$result, OffsetPaginator $pagination, SubscriptionType $type): void
    {
        $perLine = 10;
        $line = count($result);
        for ($i = 1; $i <= $pagination->getTotalPages(); $i++) {
            if ($i !== 1 && ($i - 1) % $perLine === 0) {
                $line++;
            }

            $result[$line][] = new InlineKeyboardButton((string) $i, "/$type->value:$i");
        }
    }
}
