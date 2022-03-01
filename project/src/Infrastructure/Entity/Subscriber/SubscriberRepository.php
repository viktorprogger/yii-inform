<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Entity\Subscriber;

use Cycle\ORM\ORM;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Transaction;
use RuntimeException;
use Spiral\Database\Injection\Fragment;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Settings;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberId;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;

final class SubscriberRepository implements SubscriberRepositoryInterface
{
    private Repository $cycleRepository;

    public function __construct(
        private readonly ORM $orm,
        private readonly SubscriberIdFactoryInterface $idFactory,
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
        $entity->telegram_chat_id = $subscriber->chatId;

        (new Transaction($this->orm))->persist($entity)->run();
    }

    public function find(SubscriberId $id): ?Subscriber
    {
        /** @var SubscriberEntity|null $entity */
        $entity = $this->cycleRepository->findByPK($id->value);
        if ($entity === null) {
            return null;
        }

        return $this->makeSubscriber($entity);
    }

    public function updateSettings(Subscriber $subscriber, Settings $settings): void
    {
        /** @var SubscriberEntity $entity */
        $entity = $this->cycleRepository->findByPK($subscriber->id->value); // TODO not found exception
        $entity->telegram_chat_id = $subscriber->chatId;
        $entity->settings_realtime = json_encode(array_values($settings->realtimeRepositories), JSON_THROW_ON_ERROR);
        $entity->settings_summary = json_encode(array_values($settings->summaryRepositories), JSON_THROW_ON_ERROR);
        (new Transaction($this->orm))->persist($entity)->run();
    }

    public function findForRealtimeRepo(string $repo): array
    {
        $entities = $this->cycleRepository
            ->select()
            ->where(new Fragment("JSON_CONTAINS(settings_realtime, '\"$repo\"', '$')"))
            ->fetchData();

        return array_map(fn(array $entity) => $this->idFactory->create($entity['id']), $entities);
    }

    private function makeSubscriber(?SubscriberEntity $entity): ?Subscriber
    {
        if ($entity === null) {
            return null;
        }

        $id = $this->idFactory->create($entity->id);
        $decodedRealtime = json_decode($entity->settings_realtime ?? '[]', true, flags: JSON_THROW_ON_ERROR);
        $decodedSummary = json_decode($entity->settings_summary ?? '[]', true, flags: JSON_THROW_ON_ERROR);

        return new Subscriber($id, $entity->telegram_chat_id, new Settings($decodedRealtime, $decodedSummary));
    }

    public function getAllIds(): array
    {
        $entities = $this->cycleRepository
            ->select()
            ->fetchData();

        return array_map(fn(array $entity) => $this->idFactory->create($entity['id']), $entities);
    }
}
