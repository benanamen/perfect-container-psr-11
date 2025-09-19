# Container Examples

This directory contains examples demonstrating the features of the Container class.

## Features

- **PSR-11 compliant** - Implements `ContainerInterface`
- **Autowiring** - Automatically resolves class dependencies (enabled by default)
- **Dual-mode** - Can operate with autowiring enabled or disabled
- **Smart resolution** - Understands when stored strings represent class names

## Examples

### 1. Basic Usage (`1-index.php`)
Demonstrates fundamental container usage including value storage, autowiring, and interface binding. Shows the fixed behavior where class names are automatically resolved to instances.

### 2. Value and Class Storage (`01-basic-usage.php`)
Shows how to store both simple values and class names. With the fix, class names are automatically resolved to instances when retrieved.

### 3. Factory Closures (`02-binding-closures.php`)
Demonstrates using closures as factory functions for creating services on demand.

### 4. Dependency Resolution (`03-resolving-dependencies.php`)
Shows the power of autowiring - the container automatically resolves complex dependency chains without manual configuration.

### 5. Interface Autowiring (`04-autowiring.php`)
Demonstrates the fixed behavior where interfaces can be bound to concrete implementations using `set()`, enabling full autowiring even with abstractions.

## Key Concepts

- Use `set($id, $value)` to store anything (values, instances, class names, closures)
- Class name strings are automatically resolved to instances when retrieved (the fix)
- With autowiring enabled, most classes don't need explicit registration
- Use `new Container(true)` for autowiring (default), `new Container(false)` for explicit-only mode

## Deprecation Note

The `bind()` method is deprecated and will be removed in future versions. Use `set()` for all registration.
