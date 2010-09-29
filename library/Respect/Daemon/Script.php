<?php

namespace Respect\Daemon;

class Script
{

    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

}