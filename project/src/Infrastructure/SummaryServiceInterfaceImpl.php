<?php

namespace Viktorprogger\YiisoftInform\Infrastructure;

use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Viktorprogger\YiisoftInform\Domain\SummaryServiceInterface;

final class SummaryServiceInterfaceImpl implements SummaryServiceInterface
{
    public function __construct(
        private readonly SubscriberRepositoryInterface $subscriberRepository,
    )
    {
    }

    public function getNewSummaries(): array
    {

    }
}
