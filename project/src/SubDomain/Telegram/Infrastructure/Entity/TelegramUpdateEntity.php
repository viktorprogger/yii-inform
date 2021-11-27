<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DateTimeImmutable;

#[Entity(table: 'tg_update', repository: TgUpdateEntityCycleRepository::class)]
final class TelegramUpdateEntity
{
    #[Column(type: 'int', primary: true)]
    public int $id;

    #[Column(type: 'timestamp')]
    public DateTimeImmutable $created_at;

    #[Column(type: 'longText')]
    public string $contents;
}
