<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Settings;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

final class SummaryEditAction implements ActionInterface
{
    public function __construct(
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly RepositoryButtonRepository $buttonService,
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
