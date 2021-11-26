<?php

declare(strict_types=1);

namespace Yiisoft\Inform\SubDomain\GitHub\Domain\Entity\Event;

enum EventType: string
{
    case NEW_REPO = 'repo created';

    case PR_OPENED = 'pr opened';
    case PR_CLOSED = 'pr closed';
    case PR_REOPENED = 'pr reopened';
    case PR_CHANGED = 'pr changed';
    case PR_COMMENTED = 'pr commented';
    case PR_MERGED = 'pr merged';
    case PR_MERGE_APPROVED = 'pr approved';
    case PR_MERGE_DECLINED = 'pr declined';

    case ISSUE_OPENED = 'issue opened';
    case ISSUE_REOPENED = 'issue reopened';
    case ISSUE_CLOSED = 'issue closed';
    case ISSUE_COMMENTED = 'issue commented';
}
