<?php

namespace Respect\Daemon\Adapters;

use Respect\Daemon\Job;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Respect\Env\Wrapper;

class UpstartTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        Wrapper::evil('Respect\Daemon');
        Wrapper::evil('Respect\Daemon\Adapters');
        Wrapper::set("custom");
        Wrapper::getCurrent()->setShellCallback(
            function() {
                return 'upstart';
            }
        );
        Wrapper::getCurrent()->setWritableFiles(
            array(
                '/etc/init' => true
            )
        );
        Wrapper::getCurrent()->setFileSystem(
            array(
                '/usr/bin/whoami' => 'foo'
            )
        );
        $this->object = new Upstart();
    }

    protected function tearDown()
    {
        Wrapper::set("raw");
    }

    public function testGetInstance()
    {
        $r = \Respect\Daemon\Manager::getAdapter();
        $this->assertType('Respect\Daemon\Adapters\Upstart', $r);
    }

    public function testRunsOnEnvironment()
    {
        $this->assertTrue(Upstart::runsOnEnvironment());
    }

    public function testRunsOnEnvironmentFalse()
    {
        Wrapper::getCurrent()->setShellCallback(
            function() {
                return 'command not found';
            }
        );
        $this->assertFalse(Upstart::runsOnEnvironment());
    }

    public function testRegister()
    {
        $job = new Job;
        $job->setName('test');
        $job->setPath('/usr/bin/whoami');
        $this->object->register($job);
        $lines = explode(
            PHP_EOL,
            file_get_contents(
                $this->object->getConfigDir() . DIRECTORY_SEPARATOR . 'test.conf'
            )
        );
        $this->assertContains("#test -Auto generated by RespectDeamon ", $lines);
        $this->assertContains("#resid ca76f7e9d3221b31f4cfc34d1465bae2 ", $lines);
        $this->assertContains("expect daemon ", $lines);
        $this->assertContains("start on runlevel [2345] ", $lines);
        $this->assertContains("stop on runlevel [!2345] ", $lines);
        $this->assertContains("exec /usr/bin/whoami ", $lines);
    }

    /**
     * @expectedException Respect\Daemon\Exceptions\JobAlreadyExistentException
     */
    public function testRegisterAlreadyExistent()
    {
        file_put_contents(
            $this->object->getConfigDir() . DIRECTORY_SEPARATOR . 'test.conf',
            '#test -Auto generated by RespectDeamon 
#resid INVALID  ');
        $job = new Job;
        $job->setName('test');
        $job->setPath('/usr/bin/whoami');
        $this->object->register($job);
    }

    public function testIsJobRespectMade()
    {
        $job = new Job;
        $job->setName('test');
        $job->setPath('/usr/bin/whoami');
        $this->object->register($job);
        $this->assertTrue($this->object->isJobRespectMade('test'));
    }

    public function testIsJobNotRespectMade()
    {
        file_put_contents(
            $this->object->getConfigDir() . DIRECTORY_SEPARATOR . 'test.conf',
            '#test -Auto generated by RespectDeamon 
#resid INVALID  ');
        $this->assertFalse($this->object->isJobRespectMade('test'));
    }

}