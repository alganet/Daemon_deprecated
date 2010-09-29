<?php

namespace Respect\Daemon;

class Executable implements Runnable
{

    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

}