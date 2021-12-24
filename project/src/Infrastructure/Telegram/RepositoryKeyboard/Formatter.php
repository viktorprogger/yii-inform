<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard;

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;

final class Formatter
{
    public function format(SubscriptionType $type, int $perLine, OffsetPaginator $pagination): array
    {
        $result = [];
        $count = 0;
        $line = 0;

        $result[$line][] = $this->createInlineButton('all-repos', ButtonAction::ADD, $type, 'Подписаться на все');
        $result[$line][] = $this->createInlineButton('all-repos', ButtonAction::REMOVE, $type, 'Отписаться ото всего');
        $line++;

        foreach ($pagination->read() as $button) {
            if ($count !== 0 && $count % $perLine === 0) {
                $line++;
            }
            $count++;

            $result[$line][] = $this->createInlineButton($button->name, $button->action, $type);
        }

        $this->addPagination($result, $pagination, $type);

        return $result;
    }

    public function createInlineButton(
        string $repository,
        ButtonAction $action,
        SubscriptionType $type,
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
        $callbackData = "$type->value:$sign:$repository";

        return new InlineKeyboardButton($text, $callbackData);
    }

    private function addPagination(array &$result, OffsetPaginator $pagination, SubscriptionType $type)
    {
        $perLine = 10;
        $line = count($result);
        for ($i = 1; $i <= $pagination->getTotalPages(); $i++) {
            if ($i !== 1 && ($i - 1) % $perLine === 0) {
                $line++;
            }

            $result[$line][] = new InlineKeyboardButton($i, "/$type->value:$i");
        }
    }
}
