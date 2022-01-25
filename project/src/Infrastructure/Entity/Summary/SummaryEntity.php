<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Entity\Summary;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\ORM\Promise\Reference;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber\SubscriberEntity;

#[Entity(table: 'subscriber')]
final class SummaryEntity
{
    #[Column(type: 'string', primary: true)]
    public string $id;

    #[Column(type: 'datetime', primary: true)]
    public string $time;

    #[Column(type: 'text', nullable: false)]
    public string $repositories;

    #[Column(type: 'string', nullable: false)]
    public string $subscriberId;
}
