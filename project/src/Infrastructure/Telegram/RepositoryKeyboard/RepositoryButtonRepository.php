<?php

namespace Yiisoft\Inform\Infrastructure\Telegram\RepositoryKeyboard;

use Psr\Log\LoggerInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Action\SubscriptionType;

final class RepositoryButtonRepository
{
    public function __construct(
        private readonly GithubRepositoryInterface $githubRepository,
        private readonly LoggerInterface $logger,
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

    public function createKeyboard(Subscriber $subscriber, SubscriptionType $type): RepositoryKeyboard
    {
        $keyboard = [];
        foreach ($this->githubRepository->all() as $repository) {
            $keyboard[] = $this->createButton($repository, $subscriber, $type);
        }

        return new RepositoryKeyboard(...$keyboard);
    }
}
