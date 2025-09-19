<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

$container = new Container();

// Set a closure (factory function)
$container->set('timestamp', function () {
    return time();
});

// Set a closure with dependencies
$container->set('config', function () {
    return [
        'database' => [
            'host' => 'localhost',
            'name' => 'myapp'
        ]
    ];
});

// Retrieve the results
echo "Current timestamp: " . $container->get('timestamp') . "\n";
$config = $container->get('config');
echo "Database host: " . $config['database']['host'] . "\n";
