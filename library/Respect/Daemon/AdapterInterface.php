<?php

namespace Respect\Daemon;

interface AdapterInterface
{

    public static function runsOnEnvironment();

    public function register(Job $job);

    public function isJobRespectMade($jobName);

    public function jobExists($jobName);
}
