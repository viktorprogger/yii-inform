<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

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
        $this->subscriberRepository->updateSettings(); // TODO
    }

    private function remove(string $repository, Subscriber $subscriber): void
    {
        $this->subscriberRepository->updateSettings(); // TODO
    }
}
