<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

class Dependency
{
    public function __construct()
    {
    }
}

class DependentClass
{
    public Dependency $dependency;

    public function __construct(Dependency $dependency)
    {
        $this->dependency = $dependency;
    }
}

// Set autowiring true
$container = new Container(true);

// No need to bind Dependency class
// Resolve dependencies automatically
try {
    $instance = $container->get(DependentClass::class);
    var_dump($instance);
} catch (ReflectionException $e) {
    var_dump($e);
} catch (Exception $e) {
}


