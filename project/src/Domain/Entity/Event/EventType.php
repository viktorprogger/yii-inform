<?php

declare(strict_types=1);

namespace Yiisoft\Inform\Domain\Entity\Event;

enum EventType: string
{
    case NEW_REPO = 'repo created';
    case PR_OPENED = 'pr opened';
    case PR_CHANGED = 'pr changed';
}
