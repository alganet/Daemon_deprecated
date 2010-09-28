<?php

namespace Respect\Daemon;

class EventListener
{

    protected $action;
    protected $event;

    public function __construct($action='', $event='')
    {
        $this->action = $action;
        $this->event = $event;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
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