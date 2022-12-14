<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Queue;

use Yiisoft\Yii\Queue\Message\MessageInterface;

final class RealtimeEventMessage implements MessageInterface
{
    public const NAME = 'realtime-github-event';

    private ?string $id = null;

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

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMetadata(): array
    {
        return [];
    }
}
