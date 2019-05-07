<?php

namespace Anonymous\Scheduling;


use Yii;
use Yiisoft\Mutex\Mutex;
use Yiisoft\Mutex\FileMutex;

/**
 * Class Schedule
 * @package Anonymous\Scheduling
 */
class Schedule extends \CComponent
{

    /**
     * All of the events on the schedule.
     *
     * @var Event[]
     */
    protected $_events = [];

    /**
     * The mutex implementation.
     *
     * @var Mutex
     */
    protected $_mutex;


    /**
     * Schedule constructor.
     * @throws
     */
    public function __construct()
    {
        $this->_mutex = Yii::app()->hasComponent('mutex')
            ? Yii::app()->getComponent('mutex')
            : new FileMutex(Yii::app()->runtimePath);
    }

    /**
     * Add a new callback event to the schedule.
     *
     * @param string $callback
     * @param array $parameters
     * @return Event
     * @throws
     */
    public function call($callback, array $parameters = array())
    {
        $this->_events[] = $event = new CallbackEvent($this->_mutex, $callback, $parameters);
        return $event;
    }

    /**
     * Add a new cli command event to the schedule.
     *
     * @param string $command
     * @return Event
     */
    public function command($command)
    {
        return $this->exec(PHP_BINARY . ' ' . $this->cliScriptName . ' ' . $command);
    }

    /**
     * Add a new command event to the schedule.
     *
     * @param string $command
     * @return Event
     */
    public function exec($command)
    {
        $this->_events[] = $event = new Event($this->_mutex, $command);
        return $event;
    }

    /**
     * Get events
     *
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * Get all of the events on the schedule that are due.
     *
     * @param \CConsoleApplication $app
     * @return Event[]
     */
    public function dueEvents(\CConsoleApplication $app)
    {
        return array_filter($this->_events, function (Event $event) use ($app) {
            return $event->isDue($app);
        });
    }

}
