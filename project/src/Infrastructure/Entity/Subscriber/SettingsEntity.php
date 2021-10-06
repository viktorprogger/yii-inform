<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Entity\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;

#[Embeddable(columnPrefix: 'settings_')]
final class SettingsEntity
{
    #[Column(type: 'string', nullable: true)]
    public ?string $dummy = null;
}
