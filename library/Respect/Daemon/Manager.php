<?php

namespace Respect\Daemon;

use Respect\Daemon\Exceptions\DirectoryNotFoundException;
use \UnexpectedValueException;

class Manager
{

    public static function getInstance($adapter = null)
    {
        if (!is_null($adapter)) {
            $adapterName = '\Respect\Daemon\Adapters\\' . $adapter;
            return new $adapterName;
        } else {
            $adapters = static::getAvailableAdapters();
            $adapterName = '\Respect\Daemon\Adapters\\' . array_unshift($adapters);
            return new $adapterName;
        }
        return false;
    }

    public static function getAvailableAdapters()
    {
        $adapters = array();
        foreach (new DirectoryIterator(__DIR__ . '/Adapter') as $a) {
            require_once $a->getPath();
            $adapterName = $a->getBasename('.php');
            $runs = call_user_func(
                array(
                    '\Respect\Daemon\Adapters\\' . $adapterName,
                    'runsOnCurrentEnvironment'
                )
            );
            if ($runs)
                $adapters[] = $adapterName;
        }
        return $adapters;
    }

}