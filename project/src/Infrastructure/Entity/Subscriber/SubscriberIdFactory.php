<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Entity\Subscriber;

use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberId;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Infrastructure\Entity\UuidFactory;

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
