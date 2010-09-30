<?php

namespace Respect\Daemon;

interface DeamonInterface
{

    public static function runsOnEnvironment();

    public function all();

    public function save(Job $job, $overwrite=false);

    public function remove($jobName);

    public function get($jobName);

    public function status($jobName);

    public function start($jobName);

    public function stop($jobName);
}