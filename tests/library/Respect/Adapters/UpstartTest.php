<?php

namespace Respect\Daemon\Adapters;

class UpstartTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $configDir = sys_get_temp_dir() . '/RespectEnvUnitTesting';
        @mkdir($configDir);
        $this->object = new Upstart($configDir);
    }

    public function testFoo()
    {
        
    }

}