<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Entity\Event;

use Viktorprogger\YiisoftInform\Infrastructure\Entity\UuidFactory;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventId;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\EventIdFactoryInterface;

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
