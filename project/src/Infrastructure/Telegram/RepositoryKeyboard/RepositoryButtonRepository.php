<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard;

use Psr\Log\LoggerInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;

final class RepositoryButtonRepository
{
    public function __construct(
        private readonly GithubRepositoryInterface $githubRepository,
    ) {
    }

    public function createButton(
        string $repository,
        Subscriber $subscriber,
        SubscriptionType $type,
    ): RepositoryButton {
        $currentSettings = match ($type) {
            SubscriptionType::REALTIME => $subscriber->settings->realtimeRepositories,
            SubscriptionType::SUMMARY => $subscriber->settings->summaryRepositories,
        };

        return new RepositoryButton(
            $repository,
            in_array($repository, $currentSettings, true) ? ButtonAction::REMOVE : ButtonAction::ADD,
        );
    }

    /**
     * @param Subscriber $subscriber
     * @param SubscriptionType $type
     *
     * @return RepositoryButton[]
     */
    public function createKeyboard(Subscriber $subscriber, SubscriptionType $type): array
    {
        return array_map(fn (string $repo) => $this->createButton($repo, $subscriber, $type), $this->githubRepository->all());
    }
}
