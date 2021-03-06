<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\ORM\Promise\Reference;

#[Entity(table: 'subscriber')]
final class SubscriberEntity
{
    #[Column(type: 'string', primary: true)]
    public string $id;

    #[Column(type: 'string', primary: true)]
    public string $telegram_chat_id;

    #[Column(type: 'text', nullable: true)]
    public ?string $settings_realtime = null;

    #[Column(type: 'text', nullable: true)]
    public ?string $settings_summary = null;
}
