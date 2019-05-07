<?php

namespace omnilight\scheduling\Tests;

use Anonymous\Scheduling\Event;
use PHPUnit\Framework\TestCase;
use Yiisoft\Mutex\Mutex;

class EventTest extends TestCase
{
    public function buildCommandData()
    {
        return [
            ['php -i', '/dev/null', "php -i > /dev/null 2>&1 &"],
            ['php -i', '/my folder/foo.log', "php -i > /my folder/foo.log 2>&1 &"],
        ];
    }

    /**
     * @dataProvider buildCommandData
     * @param $command
     * @param $outputTo
     * @param $result
     */
    public function testBuildCommandSendOutputTo($command, $outputTo, $result)
    {
        $stub = $this->getMockBuilder(Mutex::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $event = new Event($stub, $command);
        $event->sendOutputTo($outputTo);
        $this->assertSame($result, $event->buildCommand());
    }
}