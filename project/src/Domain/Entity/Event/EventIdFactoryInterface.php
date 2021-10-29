<?php

namespace Yiisoft\Inform\Domain\Entity\Event;

interface EventIdFactoryInterface
{
    public function create(?string $id = null): EventId;
}
