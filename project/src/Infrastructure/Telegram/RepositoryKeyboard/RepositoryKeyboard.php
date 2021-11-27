<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Telegram\RepositoryKeyboard;

use Iterator;

/**
 * TODO find a way to use generics to say PhpStorm it's a RepositoryButton[] iterator
 */
final class RepositoryKeyboard implements Iterator
{
    /** @var RepositoryButton[] */
    private readonly array $buttons;

    public function __construct(RepositoryButton ...$buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     * @param int $quantity Button quantity in a single bunch
     *
     * @return self[]
     */
    public function iterateBunch(int $quantity): iterable
    {
        $buttonCount = count($this->buttons);
        for ($offset = 0; $offset < $buttonCount; $offset += $quantity) {
            yield new self(...array_slice($this->buttons, $offset, $quantity));
        }
    }

    public function filterAction(ButtonAction $action): self
    {
        return new self(...array_filter($this->buttons, static fn (RepositoryButton $button) => $button->action === $action));
    }

    public function has(string $repository)
    {
        return in_array(
            $repository,
            array_map(
                static fn(RepositoryButton $button) => $button->name,
                $this->buttons
            ),
            true,
        );
    }

    public function current(): bool|RepositoryButton
    {
        return current($this->buttons);
    }

    public function next(): void
    {
        next($this->buttons);
    }

    public function key(): ?int
    {
        return key($this->buttons);
    }

    public function valid(): bool
    {
        return key($this->buttons) !== null;
    }

    public function rewind(): void
    {
        reset($this->buttons);
    }
}
