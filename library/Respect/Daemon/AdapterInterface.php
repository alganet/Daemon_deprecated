<?php

namespace Respect\Daemon;

interface AdapterInterface
{

    public function register(Job $job);
}
