<?php
use Clockwork\DataSource\PhpDataSource;
use li3_clockwork\extensions\StaticClockwork;

StaticClockwork::getInstance()->addDataSource(new PhpDataSource());

require __DIR__ . '/bootstrap/dispatcher.php';
