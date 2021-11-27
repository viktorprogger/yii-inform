<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client;

final class TelegramMessage
{
    /**
     * @param InlineKeyboardButton[][] $inlineKeyboard
     */
    public function __construct(
        private string $text,
        private MessageFormat $format,
        private string $chatId,
        private array $inlineKeyboard = [],
    ) {
    }

    public function getArray(): array
    {
        $result = [
            'text' => $this->text,
            'chat_id' => $this->chatId,
        ];

        if ($this->format->isMarkdown()) {
            $result['parse_mode'] = 'MarkdownV2';
        } elseif ($this->format->isHtml()) {
            $result['parse_mode'] = 'HTML';
        }

        foreach ($this->inlineKeyboard as $i => $row) {
            foreach ($row as $button) {
                $result['reply_markup']['inline_keyboard'][$i][] = [
                    'text' => $button->getLabel(),
                    'callback_data' => $button->getCallbackData(),
                ];
            }
        }

        return $result;
    }
}
