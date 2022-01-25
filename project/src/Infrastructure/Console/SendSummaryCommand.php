<?php

namespace Viktorprogger\YiisoftInform\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Viktorprogger\YiisoftInform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Queue\Message\Message;
use Yiisoft\Yii\Queue\QueueInterface;

class SendSummaryCommand extends Command
{
    protected static $defaultName = 'inform/send-summary';
    protected static $defaultDescription = 'Send changes summary to users';

    public function __construct(
        private readonly QueueInterface $queue,
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        string $name = null,
    ) {
        parent::__construct($name ?? self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * TODO
         * SummaryService should have an interface. The default implementation will send messages to a queue.
         * Another class is a message generator. It will generate message to be sent.
         */


        foreach ($this->subscriberRepository->findForSummary() as $id) {
            $this->queue->push(new Message('summary-user-id', ['id' => $id->value]));
        }

        return ExitCode::OK;
    }
}
