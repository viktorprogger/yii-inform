<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

use Yiisoft\Inform\Domain\Entity\Subscriber\Settings;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\Response;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Yiisoft\Inform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Yiisoft\Inform\SubDomain\Telegram\Domain\TelegramRequest;

final class SummaryEditAction implements ActionInterface
{
    public function __construct(
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly RepositoryButtonService $buttonService,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        [, $sign, $repository] = explode(':', $request->requestData);
        if ($sign === '+') {
            $this->add($repository, $request->subscriber);
        } else {
            $this->remove($repository, $request->subscriber);
        }

        if ($request->callbackQueryId !== null) {
            $text = match ($sign) {
                '+' => "Вы начали отслеживать $repository",
                '-' => "Вы больше не отслеживаете $repository",
            };

            $callbackResponse = new TelegramCallbackResponse($request->callbackQueryId, $text, true);
            $response = $response->withCallbackResponse($callbackResponse);
        }

        $keyboard = $this->buttonService->createKeyboard($this->subscriberRepository->find($request->subscriber->id), SubscriptionType::SUMMARY);
        $message = new TelegramKeyboardUpdate(
            $request->chatId,
            $request->messageId,
            $keyboard,
        );

        return $response->withKeyboardUpdate($message);
    }

    private function add(string $repository, Subscriber $subscriber): void
    {
        $repoList = $subscriber->settings->realtimeRepositories;
        if (!in_array($repository, $repoList, true)) {
            $repoList[] = $repository;
        }

        $this->subscriberRepository->updateSettings($subscriber, new Settings($repoList));
    }

    private function remove(string $repository, Subscriber $subscriber): void
    {
        $repoList = $subscriber->settings->realtimeRepositories;
        $repoList = array_filter($repoList, static fn(string $repo) => $repo !== $repository);

        $this->subscriberRepository->updateSettings($subscriber, new Settings($repoList));
    }
}
