<?php

namespace Respect\Daemon\Adapters;

class UpstartTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new Upstart();
    }

    public function testFoo()
    {
        $job = $this->object->getJobFromDefinition("acpid",
                '# acpid - ACPI daemon
#
# The ACPI daemon provides a socket for other daemons to multiplex kernel
# ACPI events from, and a framework for reacting to those events.

description	"ACPI daemon"

start on runlevel [2345]
stop on runlevel [!2345]

expect fork
respawn

exec acpid -c /etc/acpi/events -s /var/run/acpid.socket');
    }

    public function testBar()
    {
        $all = $this->object->all();
        foreach ($all as &$a) $a = $this->object->get($a); 
        print_r($all);
    }

}