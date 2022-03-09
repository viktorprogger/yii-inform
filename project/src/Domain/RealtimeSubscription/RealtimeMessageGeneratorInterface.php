<?php

declare(strict_types=1);

namespace Viktorprogger\YiisoftInform\Domain\RealtimeSubscription;

use Viktorprogger\TelegramBot\Domain\Client\TelegramMessage;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\Subscriber;
use Viktorprogger\YiisoftInform\SubDomain\GitHub\Domain\Entity\Event\GithubEvent;

interface RealtimeMessageGeneratorInterface
{
    public function generateForEvent(GithubEvent $event, Subscriber $subscriber): TelegramMessage;
}
