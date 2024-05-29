<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

$container = new Container();

// Bind a closure
$container->bind('greeting', function () {
    return 'Hello, world!';
});

// Retrieve the result of the closure
$greeting = $container->get('greeting');

echo $greeting; // Output: Hello, world!