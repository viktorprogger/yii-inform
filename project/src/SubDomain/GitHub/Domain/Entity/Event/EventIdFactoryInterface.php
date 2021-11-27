<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event;

interface EventIdFactoryInterface
{
    public function create(?string $id = null): EventId;
}
