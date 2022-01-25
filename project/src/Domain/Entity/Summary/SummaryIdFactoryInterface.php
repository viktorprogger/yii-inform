<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\Entity\Summary;

interface SummaryIdFactoryInterface
{
    public function create(?string $id): SummaryId;
}
