<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

$container = new Container();

// Bind a class
$container->bind('stdClass', stdClass::class);

// Retrieve an instance
try {
    $instance = $container->get('stdClass');
    var_dump($instance);
} catch (ReflectionException|Exception $e) {
}


