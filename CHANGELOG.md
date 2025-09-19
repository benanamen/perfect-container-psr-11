# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-01-01

### ⚠️ BREAKING CHANGES
- **Removed:** Deprecated `bind()` method - use `set()` instead
- **Changed:** Container instantiation now requires autowiring parameter (`new Container(true)`)
- **Changed:** Exception hierarchy now uses PSR-11 interfaces instead of RuntimeException

### 🚀 Added
- **Feature:** Full PSR-11 compliance with `ContainerInterface` implementation
- **Feature:** Intelligent class name resolution (strings automatically instantiated when appropriate)
- **Feature:** Dual-mode operation (autowiring can be enabled/disabled)
- **Feature:** Proper factory support via closures

### 🐛 Fixed
- **Fixed:** String values no longer incorrectly treated as class names
- **Fixed:** Interface dependencies now properly resolve to implementation instances
- **Fixed:** Fatal errors when calling methods on string values
- **Fixed:** Poor exception handling replaced with PSR-11 compliant exceptions

### ℹ️ Changed
- **Changed:** Consolidated API - only `set()` method for registration
- **Changed:** Improved type safety and predictable behavior
- **Changed:** Updated all examples and documentation

## [1.2.1] - 2023-12-01

### 🚀 Added
- Initial container implementation
- Basic autowiring support
- Service registration with `set()` and `bind()` methods

### ⚠️ Known Issues
- Non-PSR-11 compliant
- String values incorrectly treated as class names
- Poor exception handling
- No interface-based dependency resolution
