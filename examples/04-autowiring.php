<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

interface SomeInterface
{
    //
}

class Implementation implements SomeInterface
{
    //
}

class DependentClass2
{
    public SomeInterface $dependency;

    public function __construct(SomeInterface $dependency)
    {
        $this->dependency = $dependency;
    }
}

$container = new Container();

/*
 * Bind the implementation to the interface.
 * "bind" is deprecated. Use "set".
*/
$container->bind(SomeInterface::class, Implementation::class);
$container->set('DependentClass2', DependentClass2::class);

// Resolve dependencies automatically
try {
    $instance = $container->get(DependentClass2::class);
    var_dump($instance);
} catch (ReflectionException|Exception $e) {
}

