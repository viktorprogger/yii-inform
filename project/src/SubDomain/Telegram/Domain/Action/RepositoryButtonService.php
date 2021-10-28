<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\GithubRepository\GithubRepositoryInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\InlineKeyboardButton;

final class RepositoryButtonService
{
    public function __construct(private readonly GithubRepositoryInterface $githubRepository)
    {
    }

    public function createButton(string $repository, Subscriber $subscriber, SubscriptionType $type): InlineKeyboardButton
    {
        $currentSettings = match($type) {
            SubscriptionType::REALTIME => $subscriber->settings->realtimeRepositories,
            SubscriptionType::SUMMARY => $subscriber->settings->summaryRepositories,
        };

        if (in_array($repository, $currentSettings, true)) {
            $emoji = '➖';
            $sign = '-';
        } else {
            $emoji = '➕';
            $sign = '+';
        }

        return new InlineKeyboardButton("$emoji $repository", "$type->value:$sign:$repository");
    }

    public function createKeyboard(Subscriber $subscriber, SubscriptionType $type): array
    {
        $keyboard = [];
        $perLine = 3;
        $count = 0;
        $line = 0;

        foreach ($this->githubRepository->all() as $repository) {
            if ($count !== 0 && $count % $perLine === 0) {
                $line++;
            }
            $count++;

            $keyboard[$line][] = $this->createButton($repository, $subscriber, $type);
        }

        return $keyboard;
    }
}
