# Perfect Container PSR-11

Perfect Container PSR-11 is a lightweight, PSR-11-compliant dependency injection container designed to facilitate autowiring and flexible dependency management in PHP applications.

## Features

- Implements `Psr\Container\ContainerInterface`
- Supports autowiring to resolve dependencies automatically
- Allows manual bindings for non-autowirable classes or custom implementations

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

// Create a new container instance with autowiring enabled
autowiring = true;
$container = new Container($autowiring);
```

### Binding Dependencies

#### Bind an Instance

```php
$container->set(SomeClass::class, new SomeClass());
```

#### Bind a Closure

```php
$container->bind(SomeInterface::class, function (Container $container) {
    return new SomeImplementation($container->get(Dependency::class));
});
```

#### Bind a Value

```php
$container->bind('config', [
    'database_host' => 'localhost',
    'database_name' => 'my_database'
]);
```

### Resolving Dependencies

#### Autowired Resolution

If autowiring is enabled, dependencies will be resolved automatically:

```php
$instance = $container->get(SomeClass::class);
```

#### Check if a Binding Exists

```php
if ($container->has(SomeClass::class)) {
    echo 'Binding exists!';
}
```

---

## Exception Handling

### Not Found

If a dependency cannot be found, a `Psr\Container\NotFoundExceptionInterface` is thrown.

### Container Error

For any other errors during resolution, a `Psr\Container\ContainerExceptionInterface` is thrown.

---

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

// Binding a specific logger implementation
$container->bind(Logger::class, FileLogger::class);

// Resolving the UserService
$userService = $container->get(UserService::class);
$userService->createUser('JohnDoe');
```

---

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).
