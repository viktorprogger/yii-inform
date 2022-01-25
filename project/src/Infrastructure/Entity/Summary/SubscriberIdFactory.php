<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberId;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\UuidFactory;

final class SubscriberIdFactory implements SubscriberIdFactoryInterface
{
    public function __construct(private UuidFactory $uuidFactory)
    {
    }

    public function create(?string $id): SubscriberId
    {
        if ($id === null) {
            $id = $this->uuidFactory->create()->toString();
        }

        return new SubscriberId($id);
    }
}
