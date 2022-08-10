<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Queue;

use Yiisoft\Yii\Queue\Message\AbstractMessage;

final class RealtimeEventMessage extends AbstractMessage
{
    public const NAME = 'realtime-github-event';

    public function __construct(private readonly string $githubId, private readonly string $subscriberId)
    {
    }

    public function getHandlerName(): string
    {
        return self::NAME;
    }

    public function getData(): array
    {
        return [
            'event' => $this->githubId,
            'subscriberId' => $this->subscriberId,
        ];
    }
}
