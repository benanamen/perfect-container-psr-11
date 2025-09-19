## Features

- **PSR-11 Compliant**: Full implementation of `Psr\Container\ContainerInterface`
- **Smart Autowiring**: Automatic reflection-based dependency resolution
- **Dual Mode Operation**: Toggle autowiring on/off via constructor parameter
- **Intelligent Resolution**: Class names automatically instantiated, other values returned as-is
- **Factory Support**: Closure factories for on-demand service creation
- **Interface Binding**: Simple interface-to-implementation mapping

---

## Installation

```bash
  composer require perfectapp/perfect-container
```

---

## Usage

### Instantiation

```php
use PerfectApp\Container\Container;

// Autowiring enabled (default)
$container = new Container(true);

// Autowiring disabled (explicit registration only)  
$container = new Container(false);
```

### Registering Dependencies

```php
// Class name (automatically instantiated when retrieved)
$container->set(LoggerInterface::class, FileLogger::class);

// Ready-made instance
$container->set('database', new PDO('mysql:host=localhost;dbname=test', 'user', 'pass'));

// Closure factory
$container->set('config', function () {
    return [
        'database_host' => 'localhost',
        'database_name' => 'my_database'
    ];
});

// Simple value
$container->set('app.version', '1.0.0');
```

### Resolving Dependencies

```php
// Autowired resolution
$service = $container->get(UserService::class);

// Manual resolution
$version = $container->get('app.version');

// Check existence
if ($container->has(SomeClass::class)) {
    // Entry exists
}
```
#### Example

```php
use PerfectApp\Container\Container;

interface Logger {
    public function log(string $message): void;
}

class FileLogger implements Logger {
    public function log(string $message): void {
        file_put_contents('log.txt', $message, FILE_APPEND);
    }
}

class UserService {
    public function __construct(private Logger $logger) {}
    
    public function createUser(string $username): void {
        $this->logger->log("User $username created");
    }
}

$container = new Container(true);
$container->set(Logger::class, FileLogger::class);

$userService = $container->get(UserService::class);
$userService->createUser('john@example.com');
```

## Exception Handling

- NotFoundExceptionInterface: When a dependency cannot be found

- ContainerExceptionInterface: For errors during dependency resolution
---

## Versioning

This project follows Semantic Versioning. Current version: 2.0.0

---

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).
