<?php

namespace Respect\Daemon\Adapters;

use Respect\Daemon\Job;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class UpstartTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $configDir = sys_get_temp_dir() . '/RespectEnvUnitTesting';
        @mkdir($configDir);
        $this->object = new Upstart($configDir);
    }

    protected function tearDown()
    {
        $dir = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $this->object->getConfigDir()
                ),
                RecursiveIteratorIterator::CHILD_FIRST
        );
        for ($dir->rewind(); $dir->valid(); $dir->next()) {
            if ($dir->isDir()) {
                rmdir($dir->getPathname());
            } else {
                unlink($dir->getPathname());
            }
        }
        rmdir($this->object->getConfigDir());
    }

    public function testRegister()
    {
        $job = new Job;
        $job->setName('test');
        $job->setPath('/usr/bin/whoami');
        $this->object->register($job);
        $lines = file(
            $this->object->getConfigDir() . DIRECTORY_SEPARATOR . 'test.conf'
        );
        $this->assertContains("#test -Auto generated by RespectDeamon \n",
            $lines);
        $this->assertContains("#resid ca76f7e9d3221b31f4cfc34d1465bae2 \n",
            $lines);
        $this->assertContains("expect daemon \n", $lines);
        $this->assertContains("start on runlevel [2345] \n", $lines);
        $this->assertContains("stop on runlevel [!2345] \n", $lines);
        $this->assertContains("exec /usr/bin/whoami \n", $lines);
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