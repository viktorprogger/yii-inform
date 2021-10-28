<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Client;

final class Response
{
    /** @var TelegramMessage[] */
    private array $messages = [];

    private ?TelegramCallbackResponse $callbackResponse = null;

    /** @var TelegramMessageUpdate[] */
    private array $messageUpdates = [];

    public function withMessage(TelegramMessage $message): self
    {
        $instance = clone $this;
        $instance->messages[] = $message;

        return $instance;
    }

    public function withCallbackResponse(TelegramCallbackResponse $callbackResponse): self
    {
        $instance = clone $this;
        $instance->callbackResponse = $callbackResponse;

        return $instance;
    }

    public function withMessageUpdate(TelegramMessageUpdate $update): self
    {
        $instance = clone $this;
        $instance->messageUpdates[] = $update;

        return $instance;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getCallbackResponse(): ?TelegramCallbackResponse
    {
        return $this->callbackResponse;
    }

    public function getMessageUpdates(): array
    {
        return $this->messageUpdates;
    }
}
