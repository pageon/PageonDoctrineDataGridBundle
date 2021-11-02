<?php

namespace Pageon\DoctrineDataGridBundle\Filtration;

use Knp\Component\Pager\Event\BeforeEvent;
use Knp\Component\Pager\Event\Subscriber\Filtration\PropelQuerySubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FiltrationSubscriber implements EventSubscriberInterface
{
    /**
     * Lazy-load state tracker
     */
    private bool $isLoaded = false;

    public function before(BeforeEvent $event): void
    {
        // Do not lazy-load more than once
        if ($this->isLoaded) {
            return;
        }

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $event->getEventDispatcher();
        // hook the standard PropelQuerySubscriber and our custom QuerySubscriber
        $dispatcher->addSubscriber(new QuerySubscriber($event->getRequest()));
        $dispatcher->addSubscriber(new PropelQuerySubscriber($event->getRequest()));

        $this->isLoaded = true;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'knp_pager.before' => ['before', 1],
        ];
    }
}
