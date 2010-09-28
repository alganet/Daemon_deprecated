<?php

namespace Respect\Daemon;

class Job
{

    protected $name;
    protected $description;
    protected $main;
    protected $preStart;
    protected $postStop;
    protected $eventListeners = array();

    public function __construct($name)
    {
        
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getMain()
    {
        return $this->main;
    }

    public function getPreStart()
    {
        return $this->preStart;
    }

    public function getPostStop()
    {
        return $this->postStop;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setMain(Runnable $main)
    {
        $this->main = $main;
    }

    public function setPreStart(Script $preStart)
    {
        $this->preStart = $preStart;
    }

    public function setPostStop(Script $postStop)
    {
        $this->postStop = $postStop;
    }

    public function addEventListener(Event $event)
    {
        $this->eventListeners[spl_object_hash($event)] = $event;
    }

    public function removeEventListener(Event $event)
    {
        unset($this->eventListeners[spl_object_hash($event)]);
    }

}