<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Infrastructure\Entity\Subscriber;

use Cycle\ORM\ORM;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Transaction;
use RuntimeException;
use Yiisoft\Inform\Domain\Entity\Subscriber\Settings;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberId;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;

final class SubscriberRepository implements SubscriberRepositoryInterface
{
    private Repository $cycleRepository;

    public function __construct(
        private ORM $orm,

    ) {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->cycleRepository = $this->orm->getRepository(SubscriberEntity::class);
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
        /** @var SubscriberEntity|null $entity */
        $entity = $this->cycleRepository->findByPK($id->value);
        if ($entity === null) {
            return null;
        }

        $decodedRealtime = json_decode($entity->settings_realtime ?? '[]', true, flags: JSON_THROW_ON_ERROR);
        $decodedSummary = json_decode($entity->settings_summary ?? '[]', true, flags: JSON_THROW_ON_ERROR);

        return new Subscriber($id, new Settings($decodedRealtime, $decodedSummary));
    }

    public function updateSettings(SubscriberId $id, Settings $settings): void
    {
        /** @var SubscriberEntity $entity */
        $entity = $this->cycleRepository->findByPK($id->value);
        $entity->settings_realtime = json_encode($settings->realtimeRepositories, JSON_THROW_ON_ERROR);
        $entity->settings_summary = json_encode($settings->summaryRepositories, JSON_THROW_ON_ERROR);
        (new Transaction($this->orm))->persist($entity)->run();
    }
}
