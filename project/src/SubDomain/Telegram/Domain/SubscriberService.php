<?php

namespace Yiisoft\Inform\SubDomain\Telegram\Domain;

use Yiisoft\Inform\Domain\Entity\Subscriber\Settings;
use Yiisoft\Inform\Domain\Entity\Subscriber\Subscriber;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberIdFactoryInterface;
use Yiisoft\Inform\Domain\Entity\Subscriber\SubscriberRepositoryInterface;

final class SubscriberService
{
    public function __construct(
        private SubscriberIdFactoryInterface $subscriberIdFactory,
        private SubscriberRepositoryInterface $subscriberRepository,
    )
    {
    }

    /**
     * @param string $telegramId A telegram id of a telegram user
     *
     * @return void
     */
    public function getSubscriber(string $telegramId): Subscriber
    {
        $subscriberId = $this->subscriberIdFactory->create("tg-$telegramId");
        $subscriber = $this->subscriberRepository->find($subscriberId);
        if ($subscriber === null) {
            $subscriber = new Subscriber($subscriberId, new Settings());
            $this->subscriberRepository->create($subscriber);
        }

        return $subscriber;
    }
}
