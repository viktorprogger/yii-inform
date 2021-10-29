<?php

namespace Yiisoft\Inform\Domain\Entity\Event;

final class EventCreatedEvent
{
    public function __construct(public readonly SubscriptionEvent $subscriptionEvent)
    {
    }
}
