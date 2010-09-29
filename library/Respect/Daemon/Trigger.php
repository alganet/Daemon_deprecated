<?php

namespace Respect\Daemon;

class Trigger
{

    protected $action;
    protected $event;

    public function __construct($event, Runnable $action)
    {
        $this->action = $action;
        $this->event = $event;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction(Runnable $action)
    {
        $this->action = $action;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }

}