<?php

namespace Yiisoft\Inform\Infrastructure\Entity\GithubRepository;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'repository')]
class GithubRepositoryEntity
{
    public function __construct(
        #[Column(type: 'string', primary: true)]
        public string $name,
    ) {
    }
}
