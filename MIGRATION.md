# Migration Guide: v1.2.1 to v2.0.0
# Overview

Version 2.0 is a complete rewrite with breaking changes for PSR-11 compliance and improved functionality. This guide will help you upgrade your code.

# Breaking Changes

## 1. Namespace and Import

```php
// v1.2.1 - No namespace
$container = new Container();

// v2.0.0 - Proper namespace
use PerfectApp\Container\Container;
$container = new Container(true);
````

## 2. Instantiation Changes

```php
// v1.2.1 - No parameters, always autowired
$container = new Container();

// v2.0.0 - Explicit autowiring control
$container = new Container(true);  // Autowiring enabled (default)
$container = new Container(false); // Autowiring disabled
```

## 3. Method Changes

```php
// v1.2.1 - bind() and set() were aliases
$container->bind('key', 'value');
$container->set('key', 'value');

// v2.0.0 - Only set() is available (bind() removed)
$container->set('key', 'value');
```

## 4. Behavioral Changes
###    String Handling (CRITICAL):

```php
// v1.2.1 - ALL strings treated as class names (caused fatal errors)
$container->set('version', '1.0.0'); // ❌ Tried to instantiate class "1.0.0"
$version = $container->get('version'); // ❌ Fatal error

// v2.0.0 - Intelligent string handling
$container->set('version', '1.0.0');  // ✅ Returns string "1.0.0"
$container->set(LoggerInterface::class, FileLogger::class); // ✅ Returns FileLogger instance
````

### Instance Management:

```php
// v1.2.1 - Automatic singleton behavior
$instance1 = $container->get(MyClass::class);
$instance2 = $container->get(MyClass::class); // ✅ Same instance

// v2.0.0 - Manual singleton pattern required
$container->set(MyClass::class, function($container) {
    static $instance;
    if ($instance === null) {
        $instance = new MyClass($container->get(Dependency::class));
    }
    return $instance;
});
```

## 5. Exception Handling

```php
// v1.2.1 - Used RuntimeException with error logging
try {
    $container->get('service');
} catch (RuntimeException $e) {
    error_log($e->getMessage());
    // Handle error
}

// v2.0.0 - PSR-11 compliant exceptions
try {
    $container->get('service');
} catch (NotFoundExceptionInterface $e) {
    // Service not found
} catch (ContainerExceptionInterface $e) {
    // Container error
}
```

# Step-by-Step Migration
## 1. Update Imports

```php
// Replace:
$container = new Container();

// With:
use PerfectApp\Container\Container;
$container = new Container(true); // or false if needed
```

## 2. Replace bind() with set()

```php
// Replace all:
$container->bind('key', 'value');

// With:
$container->set('key', 'value');
```

## 3. Fix String Values

```php
// v1.2.1 behavior was broken for simple values
// v2.0.0 now works correctly:

// For simple values:
$container->set('app.version', '1.0.0');
$version = $container->get('app.version'); // ✅ Returns "1.0.0"

// For class names:
$container->set(LoggerInterface::class, FileLogger::class);
$logger = $container->get(LoggerInterface::class); // ✅ Returns FileLogger instance
```

## 4. Implement Singleton Pattern (If Needed)

```php
// If you relied on automatic singletons, add this:
$container->set(Database::class, function($container) {
    static $instance;
    if ($instance === null) {
        $instance = new Database($container->get('config'));
    }
    return $instance;
});
```

## 5. Update Exception Handling

```php
// Replace:
try {
    $service = $container->get('service');
} catch (RuntimeException $e) {
    // Old error handling
}

// With:
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

try {
    $service = $container->get('service');
} catch (NotFoundExceptionInterface $e) {
    // Service not found
} catch (ContainerExceptionInterface $e) {
    // Container error
}
```

## Migration Checklist

- [ ] **Update container instantiation with namespace**
- [ ] **Replace all `bind()` calls with `set()`**
- [ ] **Add autowiring parameter** (`true` or `false`)
- [ ] **Verify string values work correctly** (no more class instantiation attempts)
- [ ] **Implement manual singletons where needed**
- [ ] **Update exception handling for PSR-11 exceptions**
- [ ] **Test all container functionality**

## Benefits of Upgrading

- ✅ **PSR-11 compliance** - interoperability with other libraries
- ✅ **Proper string handling** - no more errors with simple values
- ✅ **Better control** - toggle autowiring as needed
- ✅ **Improved error handling** - standardized exceptions
- ✅ **Factory support** - closures for complex instantiation logic

## Need Help?

If you encounter issues during migration:

1. Check the examples in `/examples` directory
2. Review the test cases in `/tests` for usage patterns
3. Create an issue in the project repository

---

**Note**: Version 2.0.0 is not backward compatible with 1.2.1. Please test thoroughly before deploying to production.
