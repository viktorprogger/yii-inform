<?php

namespace Viktorprogger\YiisoftInform\Domain;

enum SubscriptionType: string
{
    case REALTIME = 'realtime';
    case SUMMARY = 'summary';
}
