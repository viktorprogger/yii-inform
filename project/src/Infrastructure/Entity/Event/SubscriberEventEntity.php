<?php

namespace Yiisoft\Inform\Infrastructure\Entity\Event;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DateTimeImmutable;

#[Entity(table: 'event')]
final class SubscriberEventEntity
{
    public function __construct(
        #[Column(type: 'string', primary: true)]
        public string $id,

        #[Column(type: 'string', nullable: false)]
        public string $type,

        #[Column(type: 'string', nullable: false)]
        public string $repo,

        #[Column(type: 'text', nullable: false)]
        public string $payload,

        #[Column(type: 'timestamp', nullable: false)]
        public DateTimeImmutable $created,
    ) {
    }
}
