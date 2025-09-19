<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

$container = new Container();

// Set a class name - the fix will automatically resolve it to an instance
$container->set('my_class', stdClass::class);

// Retrieve an instance (thanks to the fix)
try {
    $instance = $container->get('my_class');
    echo "Success! Retrieved instance of: " . get_class($instance) . "\n";
    var_dump($instance);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Set a simple value
$container->set('app_name', 'My Application');
echo "App Name: " . $container->get('app_name') . "\n";
