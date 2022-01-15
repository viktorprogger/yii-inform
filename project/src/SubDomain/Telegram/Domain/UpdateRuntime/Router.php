<?php

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\UpdateRuntime;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action\ActionInterface;

final class Router
{
    /**
     * @psalm-param list<array{rule: callable, action: class-string}>
     */
    private readonly array $routes;

    /**
     * @psalm-param $routes list<array{rule: callable, action: class-string}>
     */
    public function __construct(private readonly ContainerInterface $container, array $routes)
    {
        $this->routes = $routes;
    }

    public function match(TelegramRequest $request): ActionInterface
    {
        foreach ($this->routes as $route) {
            if ($route['rule']($request->requestData)) {
                return $this->container->get($route['action']);
            }
        }

        throw new NotFoundException($request);
    }
}
