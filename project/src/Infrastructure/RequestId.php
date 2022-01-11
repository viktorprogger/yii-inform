<?php

namespace Viktorprogger\YiisoftInform\Infrastructure;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;
use Viktorprogger\YiisoftInform\Infrastructure\Entity\UuidFactory;

final class RequestId
{
    private UuidInterface $uuid;

    public function __construct(private readonly UuidFactory $uuidFactory)
    {
        $this->regenerate();
    }

    public function getValue(): string
    {
        return $this->uuid?->toString() ?? throw new RuntimeException('Request ID is not generated');
    }

    public function regenerate(): void
    {
        $this->uuid = $this->uuidFactory->create();
    }
}
