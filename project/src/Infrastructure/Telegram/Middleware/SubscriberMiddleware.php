<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\Middleware;

use Viktorprogger\TelegramBot\Domain\Client\ResponseInterface;
use Viktorprogger\TelegramBot\Domain\Entity\Request\TelegramRequest;
use Viktorprogger\TelegramBot\Domain\UpdateRuntime\Middleware\MiddlewareInterface;
use Viktorprogger\TelegramBot\Domain\UpdateRuntime\RequestHandlerInterface;
use Viktorprogger\YiisoftInform\Domain\SubscriberFactory;

final class SubscriberMiddleware implements MiddlewareInterface
{
    public const ATTRIBUTE = 'subscriber';

    public function __construct(private readonly SubscriberFactory $subscriberFactory)
    {
    }

    public function process(TelegramRequest $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $subscriber = $this->subscriberFactory->getSubscriber($request->user->id->value, $request->chatId);

        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $subscriber));
    }
}
