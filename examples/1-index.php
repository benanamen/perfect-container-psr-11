<?php

use PerfectApp\Container\Container;

require_once __DIR__ . '/../vendor/autoload.php';

// Test 1: Basic value storage without autowiring
echo "=== Test 1: Basic Values (Autowiring OFF) ===\n";
$container = new Container(false);
$container->set('app.version', '1.0.0');
$container->set('app.environment', 'development');

echo "Version: " . $container->get('app.version') . "\n";
echo "Environment: " . $container->get('app.environment') . "\n\n";

// Test 2: Autowiring with class resolution
echo "=== Test 2: Autowiring (Autowiring ON) ===\n";
$container = new Container(true);

class SampleClass
{
    public string $message = 'Hello from SampleClass!';
}

$instance = $container->get(SampleClass::class);
echo $instance->message . "\n\n";

// Test 3: Interface binding with the fix
echo "=== Test 3: Interface Binding ===\n";

interface LoggerInterface
{
    public function log(string $message);
}

class FileLogger implements LoggerInterface
{
    public function log(string $message)
    {
        echo "FILE LOG: $message\n";
    }
}

// The fix makes this work correctly - string is resolved to class instance
$container->set(LoggerInterface::class, FileLogger::class);
$logger = $container->get(LoggerInterface::class);
$logger->log('This works!');
