<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event;

final class EventCreatedEvent
{
    public function __construct(public readonly GithubEvent $subscriptionEvent)
    {
    }
}
