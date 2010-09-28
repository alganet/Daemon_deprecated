<?php

namespace Respect\Daemon\Adapters;

use Respect\Daemon\Exceptions\DirectoryNotFoundException;
use \UnexpectedValueException;

class Upstart
{

    protected $dir;
    protected $dirHandle;

    public static function runsOnCurrentEnvironment()
    {
        $uname = php_uname();
        if (!stripos($uname, 'linux'))
            return false;
        return stripos(system('initctl --vesion'), 'upstart');
    }

    public function __construct()
    {
        try {
            $this->dir = trim(realpath('/etc/init'), DIRECTORY_SEPARATOR);
            $this->dirHandle = new DirectoryIterator($this->dir);
        } catch (UnexpectedValueException $e) {
            throw new DirectoryNotFoundException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    public function all()
    {
        $scripts = array();
        foreach ($this->dirHandle as $jobFile) {
            if (!$jobFile->isFile())
                continue;
            $scripts[] = $jobFile->getFilename();
        }
        return $scripts;
    }

    public function save(Job $job, $overwrite=false)
    {
        file_put_contents(
            $this->dir . DIRECTORY_SEPARATOR . $job->getName(),
            $this->getDefinition($job)
        );
    }

    public function remove($jobName)
    {
        unlink($this->dir . DIRECTORY_SEPARATOR . $job->getName());
    }

    public function get($jobName)
    {
        return $this->getJobFromDefinition(
            file_get_contents(
                $this->dir . DIRECTORY_SEPARATOR . $job->getName()
            )
        );
    }

    public function status($jobName)
    {
        return system("status $jobName");
    }

    public function start($jobName)
    {
        return system("start $jobName");
    }

    public function stop($jobName)
    {
        return system("stop $jobName");
    }

    protected function getDefinition(Job $job)
    {
        
    }

    protected function getJobFromDefinition($definition)
    {
        
    }

}