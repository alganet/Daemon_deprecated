<?php

namespace Respect\Daemon {

    use Respect\Env\Wrapper;
use ReflectionProperty;
use ReflectionClass;

    class ManagerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @expectedException Respect\Daemon\Exceptions\InvalidEnvironmentException
         */
        public function testInvalidAdapters()
        {

            Wrapper::evil('Respect\Daemon');
            Wrapper::evil('Respect\Daemon\Adapters');
            Wrapper::set("custom");
            Wrapper::getCurrent()->setShellCallback(function() {
                    return "foobar";
                });
            Manager::getAdapter();
            Wrapper::set("raw");
        }

    }

}
