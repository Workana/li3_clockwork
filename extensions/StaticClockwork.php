<?php
namespace li3_clockwork\extensions;

use Clockwork\Clockwork;

class StaticClockwork
{
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Clockwork();
        }
        return $instance;
    }
}
