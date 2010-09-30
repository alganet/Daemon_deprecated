<?php

namespace Respect\Daemon;

class Job
{

    protected $name;
    protected $meta = array();
    protected $eventListeners = array();
    protected $triggers = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getEventListeners()
    {
        return $this->eventListeners;
    }

    public function getTriggers()
    {
        return $this->triggers;
    }

    public function addTrigger(Trigger $trigger)
    {
        $this->triggers[spl_object_hash($trigger)] = $trigger;
    }

    public function removeTrigger(Trigger $trigger)
    {
        unset($this->triggers[spl_object_hash($trigger)]);
    }

    public function addMeta(Meta $meta)
    {
        $this->meta[spl_object_hash($meta)] = $meta;
    }

    public function addEventListener(EventListener $event)
    {
        $this->eventListeners[spl_object_hash($event)] = $event;
    }

}