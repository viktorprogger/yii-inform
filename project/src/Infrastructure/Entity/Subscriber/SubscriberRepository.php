<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Entity\Subscriber;

use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use RuntimeException;
use Yiisoft\Inform\Domain\Entity\Subscriber\Settings;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberId;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;

final class SubscriberRepository implements SubscriberRepositoryInterface
{

    public function __construct(
        private ORM $orm,
        private SubscriberCycleRepository $cycleRepository,
    ) {
    }

    public function create(Subscriber $subscriber): void
    {
        if ($this->cycleRepository->findByPK($subscriber->id->value)) {
            throw new RuntimeException('Subscriber with the given id already exists');
        }

        $entity = new SubscriberEntity();
        $entity->id = $subscriber->id->value;

        (new Transaction($this->orm))->persist($entity)->run();
    }

    public function find(SubscriberId $id): ?Subscriber
    {
        $entity = $this->cycleRepository->findByPK($id->value);
        if ($entity === null) {
            return null;
        }

        return new Subscriber($id, new Settings());
    }

    public function updateSettings(SubscriberId $id, Settings $settings): void
    {
        // TODO
    }
}
