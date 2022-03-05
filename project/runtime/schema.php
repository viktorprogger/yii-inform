<?php

declare(strict_types=1);

use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface as Schema;

return [
    'subscriberEntity' => [
        Schema::ENTITY => 'Viktorprogger\\YiisoftInform\\Infrastructure\\Entity\\Subscriber\\SubscriberEntity',
        Schema::MAPPER => 'Cycle\\ORM\\Mapper\\Mapper',
        Schema::SOURCE => 'Cycle\\ORM\\Select\\Source',
        Schema::REPOSITORY => 'Cycle\\ORM\\Select\\Repository',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'subscriber',
        Schema::PRIMARY_KEY => 'id',
        Schema::FIND_BY_KEYS => ['id'],
        Schema::COLUMNS => [
            'id' => 'id',
            'telegram_chat_id' => 'telegram_chat_id',
            'settings_realtime' => 'settings_realtime',
            'settings_summary' => 'settings_summary',
        ],
        Schema::RELATIONS => [],
        Schema::CONSTRAIN => null,
        Schema::TYPECAST => [],
        Schema::SCHEMA => [],
    ],
    'eventEntity' => [
        Schema::ENTITY => 'Viktorprogger\\YiisoftInform\\SubDomain\\GitHub\\Infrastructure\\Entity\\Event\\EventEntity',
        Schema::MAPPER => 'Cycle\\ORM\\Mapper\\Mapper',
        Schema::SOURCE => 'Cycle\\ORM\\Select\\Source',
        Schema::REPOSITORY => 'Cycle\\ORM\\Select\\Repository',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'github_event',
        Schema::PRIMARY_KEY => 'id',
        Schema::FIND_BY_KEYS => ['id'],
        Schema::COLUMNS => [
            'id' => 'id',
            'type' => 'type',
            'repo' => 'repo',
            'payload' => 'payload',
            'created' => 'created',
        ],
        Schema::RELATIONS => [],
        Schema::CONSTRAIN => null,
        Schema::TYPECAST => [
            'created' => 'datetime',
        ],
        Schema::SCHEMA => [],
    ],
    'githubRepositoryEntity' => [
        Schema::ENTITY => 'Viktorprogger\\YiisoftInform\\SubDomain\\GitHub\\Infrastructure\\Entity\\GithubRepository\\GithubRepositoryEntity',
        Schema::MAPPER => 'Cycle\\ORM\\Mapper\\Mapper',
        Schema::SOURCE => 'Cycle\\ORM\\Select\\Source',
        Schema::REPOSITORY => 'Cycle\\ORM\\Select\\Repository',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'repository',
        Schema::PRIMARY_KEY => 'name',
        Schema::FIND_BY_KEYS => ['name'],
        Schema::COLUMNS => [
            'name' => 'name',
        ],
        Schema::RELATIONS => [],
        Schema::CONSTRAIN => null,
        Schema::TYPECAST => [],
        Schema::SCHEMA => [],
    ],
    'userEntity' => [
        Schema::ENTITY => 'Viktorprogger\\TelegramBot\\Infrastructure\\Entity\\User\\Cycle\\UserEntity',
        Schema::MAPPER => 'Cycle\\ORM\\Mapper\\Mapper',
        Schema::SOURCE => 'Cycle\\ORM\\Select\\Source',
        Schema::REPOSITORY => 'Cycle\\ORM\\Select\\Repository',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'viktorprogger_telegram_user',
        Schema::PRIMARY_KEY => 'id',
        Schema::FIND_BY_KEYS => ['id'],
        Schema::COLUMNS => [
            'id' => 'id',
        ],
        Schema::RELATIONS => [],
        Schema::CONSTRAIN => null,
        Schema::TYPECAST => [],
        Schema::SCHEMA => [],
    ],
    'requestEntity' => [
        Schema::ENTITY => 'Viktorprogger\\TelegramBot\\Infrastructure\\Entity\\Request\\Cycle\\RequestEntity',
        Schema::MAPPER => 'Cycle\\ORM\\Mapper\\Mapper',
        Schema::SOURCE => 'Cycle\\ORM\\Select\\Source',
        Schema::REPOSITORY => 'Cycle\\ORM\\Select\\Repository',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'viktorprogger_telegram_request',
        Schema::PRIMARY_KEY => 'id',
        Schema::FIND_BY_KEYS => ['id'],
        Schema::COLUMNS => [
            'id' => 'id',
            'created_at' => 'created_at',
            'contents' => 'contents',
        ],
        Schema::RELATIONS => [],
        Schema::CONSTRAIN => null,
        Schema::TYPECAST => [
            'id' => 'int',
            'created_at' => 'datetime',
        ],
        Schema::SCHEMA => [],
    ],
];
