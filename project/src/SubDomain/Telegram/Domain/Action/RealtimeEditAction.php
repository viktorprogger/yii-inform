<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\Domain\Entity\Subscriber\Settings;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

class RealtimeEditAction implements ActionInterface
{
    public function __construct(private readonly SubscriberRepositoryInterface $subscriberRepository)
    {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        [, $sign, $repository] = explode(':', $request->request);
        if ($sign === '+') {
            $this->add($repository, $request->subscriber);
        } else {
            $this->remove($repository, $request->subscriber);
        }

        // TODO Send a tooltip and update the message on which the button was clicked

        return $response;
    }

    private function add(string $repository, Subscriber $subscriber): void
    {
        $repoList = $subscriber->settings->realtimeRepositories;
        if (!in_array($repository, $repoList, true)) {
            $repoList[] = $repository;
        }

        $this->subscriberRepository->updateSettings($subscriber->id, new Settings($repoList));
    }

    private function remove(string $repository, Subscriber $subscriber): void
    {
        $repoList = $subscriber->settings->realtimeRepositories;
        $repoList = array_filter($repoList, static fn(string $repo) => $repo !== $repository);

        $this->subscriberRepository->updateSettings($subscriber->id, new Settings($repoList));
    }
}
