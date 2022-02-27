<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Middleware;

use Viktorprogger\TelegramBot\Domain\Client\ResponseInterface;
use Viktorprogger\TelegramBot\Domain\Entity\Request\TelegramRequest;
use Viktorprogger\TelegramBot\Domain\UpdateRuntime\Middleware\MiddlewareInterface;
use Viktorprogger\TelegramBot\Domain\UpdateRuntime\RequestHandlerInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Settings;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;

final class SubscriberMiddleware implements MiddlewareInterface
{
    public const ATTRIBUTE = 'subscriber';

    public function __construct(
        private readonly SubscriberIdFactoryInterface $idFactory,
        private readonly SubscriberRepositoryInterface $repository,
    ) {
    }

    public function process(TelegramRequest $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $id = $this->idFactory->create($request->user->id->value);
        $subscriber = $this->repository->find($id);
        if ($subscriber === null) {
            $subscriber = new Subscriber($id, $request->chatId, new Settings());
            $this->repository->create($subscriber);
        }

        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $subscriber));
    }
}
