<?php

namespace Yiisoft\Inform\SubDomain\GitHub\Infrastructure\Entity\GithubRepository;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'repository')]
final class GithubRepositoryEntity
{
    public function __construct(
        #[Column(type: 'string', primary: true)]
        public string $name,
    ) {
    }
}
