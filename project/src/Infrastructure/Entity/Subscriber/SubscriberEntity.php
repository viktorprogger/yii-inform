<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Entity\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\ORM\Promise\Reference;

#[Entity(table: 'subscriber', repository: SubscriberCycleRepository::class)]
final class SubscriberEntity
{
    #[Column(type: 'string', primary: true)]
    public string $id;

    #[Column(type: 'text', nullable: true)]
    public ?string $settings_realtime;

    #[Column(type: 'text', nullable: true)]
    public ?string $settings_summary;
}
