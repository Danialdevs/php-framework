<?php

namespace App\Providers;

use App\Listeners\ContentLengthListener;
use App\Listeners\HandleEntityListener;
use App\Listeners\InternalErrorListener;
use Danial\Framework\Dbal\Event\EntityPersist;
use Danial\Framework\Event\EventDispatcher;
use Danial\Framework\Http\Events\ResponseEvent;
use Danial\Framework\Providers\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    private array $listen = [
        ResponseEvent::class => [
            InternalErrorListener::class,
            ContentLengthListener::class,
        ],
        EntityPersist::class => [
            HandleEntityListener::class,
        ],
    ];

    public function __construct(
        private readonly EventDispatcher $eventDispatcher
    ) {
    }

    public function register(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                $this->eventDispatcher->addListener($event, new $listener);
            }
        }
    }
}
