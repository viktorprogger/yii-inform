<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;

#[Embeddable(columnPrefix: 'settings_')]
class SettingsEntity
{
    public function __construct(
        #[Column(type: 'text', nullable: false)]
        public string $realtime = '[]',

        #[Column(type: 'text', nullable: false)]
        public string $summary = '[]',
    ) {
    }
}
