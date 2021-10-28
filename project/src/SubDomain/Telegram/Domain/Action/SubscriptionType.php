<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain\Action;

enum SubscriptionType: string
{
    case REALTIME = 'realtime';
    case SUMMARY = 'summary';
}
