<?php

namespace Viktorprogger\YiisoftInform\SubDomain\Telegram\Domain\Action;

enum SubscriptionType: string
{
    case REALTIME = 'realtime';
    case SUMMARY = 'summary';
}
