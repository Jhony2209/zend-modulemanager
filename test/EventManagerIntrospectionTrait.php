<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ModuleManager;

use ReflectionProperty;
use Zend\EventManager\EventManager;

/**
 * Offer methods for introspecting event manager events and listeners.
 */
trait EventManagerIntrospectionTrait
{
    /**
     * Retrieve a list of event names from an event manager.
     *
     * @param EventManager $events
     * @return string[]
     */
    private function getEventsFromEventManager(EventManager $events)
    {
        $r = new ReflectionProperty($events, 'events');
        $r->setAccessible(true);
        $listeners = $r->getValue($events);
        return array_keys($listeners);
    }

    /**
     * Retrieve an interable list of listeners for an event.
     *
     * Given an event and an event manager, returns an iterator with the
     * listeners for that event, in priority order.
     *
     * If $withPriority is true, the key values will be the priority at which
     * the given listener is attached.
     *
     * Do not pass $withPriority if you want to cast the iterator to an array,
     * as many listeners will likely have the same priority, and thus casting
     * will collapse to the last added.
     *
     * @param string $event
     * @param EventManager $events
     * @param bool $withPriority
     * @return \Traversable
     */
    private function getListenersForEvent($event, EventManager $events, $withPriority = false)
    {
        $r = new ReflectionProperty($events, 'events');
        $r->setAccessible(true);
        $listeners = $r->getValue($events);

        if (! isset($listeners[$event])) {
            return $this->traverseListeners([]);
        }

        return $this->traverseListeners($listeners[$event], $withPriority);
    }

    /**
     * Assert that a given listener exists at the specified priority.
     *
     * @param callable $expectedListener
     * @param int $expectedPriority
     * @param string $event
     * @param EventManager $events
     * @param string $message Failure message to use, if any.
     */
    private function assertListenerAtPriority(
        callable $expectedListener,
        $expectedPriority,
        $event,
        EventManager $events,
        $message = ''
    ) {
        $message   = $message ?: sprintf(
            'Listener not found for event "%s" and priority %d',
            $event,
            $expectedPriority
        );
        $listeners = $this->getListenersForEvent($event, $events, true);
        $found     = false;
        foreach ($listeners as $priority => $listener) {
            if ($listener === $expectedListener
                && $priority === $expectedPriority
            ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, $message);
    }

    /**
     * Returns an indexed array of listeners for an event.
     *
     * Returns an indexed array of listeners for an event, in priority order.
     * Priority values will not be included; use this only for testing if
     * specific listeners are present, or for a count of listeners.
     *
     * @param string $event
     * @param EventManager $events
     * @return callable[]
     */
    private function getArrayOfListenersForEvent($event, EventManager $events)
    {
        return iterator_to_array($this->getListenersForEvent($event, $events));
    }

    /**
     * Generator for traversing listeners in priority order.
     *
     * @param array $listeners
     * @param bool $withPriority When true, yields priority as key.
     */
    public function traverseListeners(array $queue, $withPriority = false)
    {
        krsort($queue, SORT_NUMERIC);

        foreach ($queue as $priority => $listeners) {
            $priority = (int) $priority;
            foreach ($listeners as $listener) {
                if ($withPriority) {
                    yield $priority => $listener;
                } else {
                    yield $listener;
                }
            }
        }
    }
}
