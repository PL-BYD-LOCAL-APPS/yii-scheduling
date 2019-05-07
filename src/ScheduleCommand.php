<?php

namespace Anonymous\Scheduling;

/**
 * Class ScheduleCommand
 * @package Anonymous\Scheduling
 */
abstract class ScheduleCommand extends \CConsoleCommand
{

    /**
     * @var Schedule
     */
    protected $schedule = 'schedule';


    /**
     * Jobs declaration method
     * @param Schedule $schedule
     * @return mixed
     */
    abstract protected function schedule(Schedule $schedule);

    /**
     * @inheritDoc
     * @throws \CException
     */
    public function init()
    {
        if (\Yii::app()->hasComponent($this->schedule)) {
            $this->schedule = \Yii::app()->getComponent($this->schedule);
        } else {
            $this->schedule = \Yii::createComponent(Schedule::class);
        }

        parent::init();
    }

    /**
     * Run action
     */
    public function actionRun()
    {
        /** @var \CConsoleApplication $app */
        $app = \Yii::app();

        $this->schedule($this->schedule);
        $events = $this->schedule->dueEvents($app);

        foreach ($events as $event) {
            $this->stdout('Running scheduled command: '.$event->getSummaryForDisplay()."\n");
            $event->run($app);
        }

        if (count($events) === 0)
        {
            $this->stdout("No scheduled commands are ready to run.\n");
        }
    }

    /**
     * Prints a string to STDOUT.
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public function stdout($string)
    {
        return fwrite(\STDOUT, $string);
    }

    /**
     * Prints a string to STDERR.
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public function stderr($string)
    {
        return fwrite(\STDERR, $string);
    }

}