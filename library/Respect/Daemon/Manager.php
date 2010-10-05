<?php

namespace Respect\Daemon;

use DirectoryIterator;

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

}