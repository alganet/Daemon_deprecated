<?php

namespace Respect\Daemon\Adapters;

use Respect\Daemon\Exceptions\DirectoryNotFoundException;
use \UnexpectedValueException;
use \DirectoryIterator;
use Respect\Daemon\Job;
use Respect\Daemon\Meta;
use Respect\Daemon\Trigger;
use Respect\Daemon\EventListener;
use Respect\Daemon\Executable;

class Upstart
{

    protected $dir;
    protected $dirHandle;

    public static function runsOnEnvironment()
    {
        $uname = php_uname();
        if (!stripos($uname, 'linux'))
            return false;
        return false !== stripos(system('initctl --vesion'), 'upstart');
    }

    public function __construct($dir='/etc/init')
    {
        try {
            $this->dir = rtrim(realpath($dir), DIRECTORY_SEPARATOR);
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
            $scripts[] = $jobFile->getBasename('.conf');
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
            $jobName,
            file_get_contents(
                $this->dir . DIRECTORY_SEPARATOR . $jobName . '.conf'
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

    public function getJobFromDefinition($name, $definition)
    {
        $lines = explode(PHP_EOL, $definition);
        $job = new Job($name);
        $previousStanza = null;
        $scriptData = array();
        foreach ($lines as $l) {
            $stanza = $this->findStanza($l);
            if (
                (stripos($previousStanza, 'script') !== false
                && $previousStanza !== 'end script'
                ) || !empty($scriptData)) {
                $scriptData[] = $l;
            }
            switch ($stanza) {
                //meta
                case 'author':
                case 'console':
                case 'description':
                case 'emits':
                case 'env':
                case 'expect':
                case 'export':
                case 'instance':
                case 'kill':
                case 'normal exit':
                case 'oom':
                case 'respawn':
                case 'task':
                case 'umask':
                    $job->addMeta(
                        new Meta(
                            $stanza,
                            $this->findSingleValue($stanza, $l)
                        )
                    );
                    break;
                //script
                case 'script':
                case 'post-start script':
                case 'post-stop script':
                case 'pre-start script':
                case 'pre-stop script':
                    $scriptData[] = trim(str_replace('script', '', $stanza)) ? : 'main';
                    break;
                case 'end script':
                    $job->addTrigger(
                        new Trigger(
                            array_shift($scriptData),
                            new Script(implode(PHP_EOL, $scriptData))
                        )
                    );
                    $scriptData = array();
                    break;
                //events
                case 'exec':
                case 'post-start exec':
                case 'post-stop exec':
                case 'pre-start exec':
                case 'pre-stop exec':
                    $job->addTrigger(
                        new Trigger(
                            trim(str_replace('exec', '', $stanza)) ? : 'main',
                            new Executable($this->findSingleValue($stanza, $l))
                        )
                    );
                    break;
                case 'start on':
                case 'stop on':
                    $job->addEventListener(
                        new EventListener(
                            trim(str_replace('on', '', $stanza)),
                            $this->findSingleValue($stanza, $l)
                        )
                    );
                    break;
                //not implemented
                default:
                    break;
            }
            $previousStanza = $stanza;
        }
        return $job;
    }

    protected function findSingleValue($stanza, $line)
    {
        if ($this->findStanza($line) !== $stanza)
            return;
        return trim(str_replace($stanza, '', $line), " \n\t\r\0\x0B\"");
    }

    protected function findStanza($line)
    {
        $stanzas = array(
            'author',
            'chdir',
            'chroot',
            'console',
            'description',
            'emits',
            'env',
            'exec',
            'expect',
            'export',
            'instance',
            'kill',
            'limit',
            'nice',
            'normal exit',
            'oom',
            'post-start exec',
            'post-stop exec',
            'pre-start exec',
            'pre-stop exec',
            'post-start script',
            'post-stop script',
            'pre-start script',
            'pre-stop script',
            'respawn',
            'script',
            'session leader',
            'start on',
            'stop on',
            'task',
            'umask',
            'version'
        );
        $line = trim($line);
        foreach ($stanzas as $s)
            if (stripos($line, $s) === 0)
                return $s;
    }

}