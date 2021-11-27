<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Settings;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;

final class RealtimeEditAction implements ActionInterface
{
    public function __construct(
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly RepositoryButtonRepository $buttonService,
        private readonly Formatter $formatter,
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

        $response = $this->sendCallbackResponse($request, $sign, $repository, $response);

        return $this->sendKeyboardUpdate($request, $response, $repository);
    }

    private function add(string $repository, Subscriber $subscriber): void
    {
        $repoList = $subscriber->settings->realtimeRepositories;
        if (!in_array($repository, $repoList, true)) {
            $repoList[] = $repository;
        }

        $settings = new Settings($repoList, $subscriber->settings->summaryRepositories);
        $this->subscriberRepository->updateSettings($subscriber, $settings);
    }

    private function remove(string $repository, Subscriber $subscriber): void
    {
        $repoList = $subscriber->settings->realtimeRepositories;
        $repoList = array_filter($repoList, static fn(string $repo) => $repo !== $repository);

        $this->subscriberRepository->updateSettings($subscriber, new Settings($repoList, $subscriber->settings->summaryRepositories));
    }

    /**
     * @param TelegramRequest $request
     * @param mixed $sign
     * @param string $repository
     * @param Response $response
     *
     * @return Response
     */
    private function sendCallbackResponse(
        TelegramRequest $request,
        mixed $sign,
        string $repository,
        Response $response
    ): Response {
        if ($request->callbackQueryId !== null) {
            $text = match ($sign) {
                '+' => "Вы начали отслеживать $repository",
                '-' => "Вы больше не отслеживаете $repository",
            };

            $callbackResponse = new TelegramCallbackResponse($request->callbackQueryId, $text, true);
            $response = $response->withCallbackResponse($callbackResponse);
        }

        return $response;
    }

    /**
     * @param TelegramRequest $request
     * @param Response $response
     * @param string $repository
     *
     * @return Response
     */
    private function sendKeyboardUpdate(TelegramRequest $request, Response $response, string $repository): Response
    {
        $keyboard = $this->buttonService->createKeyboard(
            $this->subscriberRepository->find($request->subscriber->id),
            SubscriptionType::REALTIME
        );

        foreach ($keyboard->iterateBunch(100) as $subKeyboard) {
            if ($subKeyboard->has($repository)) {
                $message = new TelegramKeyboardUpdate(
                    $request->chatId,
                    $request->messageId,
                    $this->formatter->format($subKeyboard, SubscriptionType::REALTIME),
                );

                $response = $response->withKeyboardUpdate($message);
                break;
            }
        }

        // TODO What to do if there was no such button in the keyboard??
        return $response;
    }
}
