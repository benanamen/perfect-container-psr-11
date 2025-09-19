# Perfect Container PSR-11

Perfect Container PSR-11 is a lightweight, PSR-11-compliant dependency injection container designed to facilitate autowiring and flexible dependency management in PHP applications.

## Features

- **PSR-11 Compliant**: Implements `Psr\Container\ContainerInterface`
- **Smart Autowiring**: Automatically resolves class dependencies via reflection
- **Dual Mode Operation**: Supports both autowiring-enabled and explicit-only modes
- **Intelligent Resolution**: Automatically converts class name strings to instances when retrieved
- **Factory Support**: Closure factories for on-demand service creation
- **Interface Binding**: Map interfaces to concrete implementations

---

## Installation

Download the library and include it in your project using your preferred method. If using Composer:

```bash
  composer require perfectapp/perfect-container-psr-11
```

---

## Usage

### Instantiation

```php
use PerfectApp\Container\Container;

// Create a new container instance with autowiring enabled (default)
$container = new Container(true);

// Or with autowiring disabled (explicit registration only)
$container = new Container(false);
```

### Registering Dependencies

#### Register a Class Name (Automatically Instantiated)

```php
// The container will automatically instantiate FileLogger when retrieved
$container->set(LoggerInterface::class, FileLogger::class);
```

#### Register a Ready-Made Instance

```php
$container->set('database', new PDO('mysql:host=localhost;dbname=test', 'user', 'pass'));
```

#### Register a Closure Factory

```php
$container->set('config', function () {
    return [
        'database_host' => 'localhost',
        'database_name' => 'my_database'
    ];
});
```

#### Register a Simple Value

```php
$container->set('app.version', '1.0.0');
```

### Resolving Dependencies

#### Autowired Resolution (When Enabled)

```php
// Automatically resolves dependencies via reflection
$service = $container->get(UserService::class);
```

#### Manual Resolution

```php
$config = $container->get('app.version');
```

#### Check if a Entry Exists

```php
if ($container->has(SomeClass::class)) {
    echo 'Entry exists!';
}
```
---

## Key Behaviors

* Class Name Strings: When you set() a class name string, get() returns an instance of that class
* Objects: When you set() an object, get() returns that exact object
* Closures: When you set() a closure, get() executes it and returns the result
* Other Values: When you set() any other value, get() returns that value directly

## Example

```php
namespace App;

use PerfectApp\Container\Container;

interface Logger {
    public function log(string $message): void;
}

class FileLogger implements Logger {
    public function log(string $message): void
    {
        file_put_contents('log.txt', $message . PHP_EOL, FILE_APPEND);
    }
}

class UserService {
    public function __construct(private Logger $logger) {}

    public function createUser(string $username): void
    {
        // Business logic here
        $this->logger->log("User $username created.");
    }
}

$container = new Container(true);

// Bind interface to implementation (returns FileLogger instance)
$container->set(Logger::class, FileLogger::class);

// Resolve the UserService with automatic dependency injection
$userService = $container->get(UserService::class);
$userService->createUser('JohnDoe');
```



## Exception Handling

### Not Found Exception

If a dependency cannot be found, a `Psr\Container\NotFoundExceptionInterface` is thrown.

### Container Exception

For any other errors during resolution, a `Psr\Container\ContainerExceptionInterface` is thrown.

---

## Versioning

This project follows Semantic Versioning. Current version: 0.2.0

---

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).
