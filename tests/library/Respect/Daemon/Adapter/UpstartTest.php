<?php

namespace Respect\Daemon\Adapters;

use Respect\Daemon;

class UpstartTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new Upstart();
    }

    public function testFoo()
    {
        $job = new Daemon\Job("test");
        $job->addTrigger(new Daemon\Trigger("main", new Daemon\Script("echo 1")));
        echo $this->object->getDefinition($job);
        $job = new Daemon\Job("test");
        $job->addTrigger(new Daemon\Trigger("main", new Daemon\Executable("php")));
        echo $this->object->getDefinition($job);
    }

}