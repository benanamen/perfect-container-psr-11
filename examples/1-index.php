<?php

use PerfectApp\Container\Container;

require_once __DIR__ . '/../vendor/autoload.php';

// Test the container without autowiring
$container = new Container(false);
$container->set('foo', 'bar');

try {
    echo $container->get('foo');
} catch (ReflectionException|Exception $e) {
} // Output: bar


$container->bind('biz', 'baz');
try {
    echo $container->get('biz');
} catch (ReflectionException|Exception $e) {
} // Output: baz

// Test the container with autowiring
$container = new Container(true);

class SampleClass
{
    public string $prop = 'Hello';
}

try {
    $instance = $container->get('SampleClass');
    echo $instance->prop; // Output: Hello
} catch (ReflectionException|Exception $e) {
}

