<?php

namespace Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Entity\Event;

use Yiisoft\Inform\Infrastructure\Entity\UuidFactory;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventId;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;

final class EventIdFactory implements EventIdFactoryInterface
{
    public function __construct(private UuidFactory $uuidFactory)
    {
    }

    public function create(?string $id = null): EventId
    {
        if ($id === null) {
            $id = $this->uuidFactory->create()->toString();
        }

        return new EventId($id);
    }
}
