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

    public function createButton(string $repository, Subscriber $subscriber): InlineKeyboardButton
    {
        if (in_array($repository, $subscriber->settings->realtimeRepositories, true)) {
            $emoji = '➖';
            $sign = '-';
        } else {
            $emoji = '➕';
            $sign = '+';
        }

        return new InlineKeyboardButton("$emoji $repository", "realtime:$sign:$repository");
    }

    public function createKeyboard(Subscriber $subscriber): array
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

            $keyboard[$line][] = $this->createButton($repository, $subscriber);
        }

        return $keyboard;
    }
}
