<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard;

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;

final class Formatter
{
    public function format(RepositoryKeyboard $keyboard, SubscriptionType $type): array
    {
        $result = [];
        $perLine = 3;
        $count = 0;
        $line = 0;

        /** @var RepositoryButton $button */
        foreach ($keyboard as $button) {
            if ($count !== 0 && $count % $perLine === 0) {
                $line++;
            }
            $count++;

            $result[$line][] = $this->createInlineButton($button->name, $button->action, $type);
        }

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
}
