<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

class DatabaseConnection
{
    public function __construct()
    {
        echo "DatabaseConnection created\n";
    }
}

class UserRepository
{
    public DatabaseConnection $db;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
        echo "UserRepository created with Database dependency\n";
    }
}

// Autowiring enabled - no need to manually set dependencies
$container = new Container(true);

try {
    // The container automatically resolves UserRepository and its DatabaseConnection dependency
    $userRepo = $container->get(UserRepository::class);
    echo "Successfully resolved dependency chain!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
