<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'subscriber')]
class SubscriberEntity
{
    #[Column(type: 'string', primary: true)]
    public string $id;

    #[Column(type: 'string', nullable: false)]
    public string $telegram_chat_id;

    #[Column(type: 'text', nullable: true)]
    public ?string $settings_realtime = null;

    #[Column(type: 'text', nullable: true)]
    public ?string $settings_summary = null;
}
