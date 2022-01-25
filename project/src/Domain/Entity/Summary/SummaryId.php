<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\Entity\Summary;

final class SummaryId
{
    public function __construct(public readonly string $value)
    {
    }
}
