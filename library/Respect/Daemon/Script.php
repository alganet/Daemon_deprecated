<?php

namespace Respect\Daemon;

class Script implements Runnable
{

    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

}