<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Action;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Settings;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\Formatter;
use Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard\RepositoryButtonRepository;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\GithubRepositoryInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\ActionInterface;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\SubscriptionType;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\Response;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramCallbackResponse;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Client\TelegramKeyboardUpdate;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime\TelegramRequest;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;

final class RealtimeEditAction implements ActionInterface
{
    public function __construct(
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly RepositoryButtonRepository $buttonService,
        private readonly Formatter $formatter,
        private readonly GithubRepositoryInterface $githubRepository,
    ) {
    }

    public function handle(TelegramRequest $request, Response $response): Response
    {
        [, $sign, $repository, $page] = explode(':', $request->requestData);
        $page = (int) ($page ?? 1);

        if ($sign === '+') {
            $keyboardChanged = $this->add($repository, $request->subscriber);
        } else {
            $keyboardChanged = $this->remove($repository, $request->subscriber);
        }

        $response = $this->sendCallbackResponse($request, $sign, $repository, $response);
        if ($keyboardChanged) {
            $response = $this->sendKeyboardUpdate($request, $response, $page);
        }

        return $response;
    }

    private function add(string $repository, Subscriber $subscriber): bool
    {
        if ($repository === Formatter::REPO_ALL) {
            $allRepos = $this->githubRepository->all();
            if (array_diff($allRepos, $subscriber->settings->realtimeRepositories) !== []) {
                $settings = new Settings($allRepos, $subscriber->settings->summaryRepositories);
                $this->subscriberRepository->updateSettings($subscriber, $settings);

                return true;
            }

            return false;
        }

        $repoList = $subscriber->settings->realtimeRepositories;
        if (!in_array($repository, $repoList, true)) {
            $repoList[] = $repository;
            $settings = new Settings($repoList, $subscriber->settings->summaryRepositories);
            $this->subscriberRepository->updateSettings($subscriber, $settings);

            return true;
        }

        return false;
    }

    private function remove(string $repository, Subscriber $subscriber): bool
    {
        if ($repository === Formatter::REPO_ALL) {
            if ($subscriber->settings->realtimeRepositories === []) {
                return false;
            }

            $settings = new Settings([], $subscriber->settings->summaryRepositories);
            $this->subscriberRepository->updateSettings($subscriber, $settings);

            return true;
        }

        $repoList = $subscriber->settings->realtimeRepositories;
        if (in_array($repository, $repoList, true)) {
            $repoList = array_filter($repoList, static fn(string $repo) => $repo !== $repository);

            $this->subscriberRepository->updateSettings(
                $subscriber,
                new Settings(
                    $repoList,
                    $subscriber->settings->summaryRepositories
                )
            );

            return true;
        }

        return false;
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
    ): Response
    {
        if ($request->callbackQueryId !== null) {
            $text = match($repository) {
                Formatter::REPO_ALL => match ($sign) {
                    '+' => "Вы начали отслеживать все репозитории",
                    '-' => "Вы больше не отслеживаете ни один репозиторий",
                },
                default => match ($sign) {
                    '+' => "Вы начали отслеживать $repository",
                    '-' => "Вы больше не отслеживаете $repository",
                },
            };

            $callbackResponse = new TelegramCallbackResponse($request->callbackQueryId, $text);
            $response = $response->withCallbackResponse($callbackResponse);
        }

        return $response;
    }

    /**
     * @param TelegramRequest $request
     * @param Response $response
     * @param int $page
     *
     * @return Response
     */
    private function sendKeyboardUpdate(TelegramRequest $request, Response $response, int $page): Response
    {
        $buttons = $this->buttonService->createKeyboard(
            $this->subscriberRepository->find($request->subscriber->id),
            SubscriptionType::REALTIME
        );

        $pagination = (new OffsetPaginator(new IterableDataReader($buttons)))
            ->withPageSize(21)
            ->withCurrentPage($page);

        $update = new TelegramKeyboardUpdate(
            $request->chatId,
            $request->messageId,
            $this->formatter->format(SubscriptionType::REALTIME, 3, $pagination),
        );

        return $response->withKeyboardUpdate($update);
    }
}
