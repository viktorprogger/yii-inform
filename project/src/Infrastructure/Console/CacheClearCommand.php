<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Console;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;

final class CacheClearCommand extends Command
{
    protected static $defaultName = 'inform/cache/clear';
    protected static $defaultDescription = 'Clear all cache';

    public function __construct(private readonly CacheInterface $cache)
    {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cache->clear();

        return ExitCode::OK;
    }
}
