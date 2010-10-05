<?php

namespace Respect\Daemon;

use DirectoryIterator;
use Respect\Daemon\Job;

class Manager
{

    public static function getAdapter($name=null)
    {
        $adapterNs = 'Respect\Daemon\Adapters\\';
        if (!is_null($name)) {
            $name = $adaptersNs . ucfirst($name);
            return new $name;
        } else {
            $adaptersDir = __DIR__ . DIRECTORY_SEPARATOR . 'Adapters';
            foreach (new DirectoryIterator($adaptersDir) as $adapterFile) {
                if ($adapterFile->isFile()) {
                    $adapterName = $adapterFile->getBasename('.php');
                    $adapterClassName = $adapterNs . $adapterName;
                    $validEnv = call_user_func(
                        array(
                            $adapterClassName,
                            'runsOnEnvironment'
                        )
                    );
                    if ($validEnv)
                        return new $adapterClassName;
                }
            }
        }
    }

    public static function register($name, $path)
    {
        $adapter = self::getAdapter();
        if ($adapter->isJobRespectMade($name))
            return true;
        elseif ($adaopter->jobExists($name))
            throw new Exceptions\InvalidJobException(
                sprintf('Job %s exists but isnt manageable by Respect', $name)
            );
        $job = new Job();
        $job->setName($name);
        $job->setPath($path);
        $job->setDescription('Auto-registered job ' . $name);
        $adapter->register($job);
        return true;
    }

}