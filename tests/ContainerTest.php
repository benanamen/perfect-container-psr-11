<?php declare(strict_types=1);

namespace PerfectApp\Tests;

use Exception;
use PerfectApp\Container\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    public function testGetWithoutAutowiring(): void
    {
        $container = new Container(false);
        $container->set('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    public function testGetWithAutowiring(): void
    {
        $container = new Container(true);

        $instance = $container->get(SampleClass::class);
        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertEquals('Hello', $instance->prop);
    }

    public function testGetWithDependencyAutowiring(): void
    {
        $container = new Container(true);

        $service = $container->get(ServiceClass::class);
        $this->assertInstanceOf(ServiceClass::class, $service);
        $this->assertInstanceOf(SampleClass::class, $service->dependency);
    }

    public function testGetWithInterfaceBinding(): void
    {
        $container = new Container(true);
        $container->set(LoggerInterface::class, TestFileLogger::class);

        $logger = $container->get(LoggerInterface::class);
        $this->assertInstanceOf(TestFileLogger::class, $logger);
    }

    public function testGetWithStringValue(): void
    {
        $container = new Container(false);
        $container->set('version', '1.0.0');

        $this->assertEquals('1.0.0', $container->get('version'));
    }

    public function testGetWithClosureFactory(): void
    {
        $container = new Container(false);
        $container->set('timestamp', function (): int {
            return time();
        });

        $timestamp = $container->get('timestamp');
        $this->assertIsInt($timestamp);
    }

    public function testGetNotFound(): void
    {
        $container = new Container(false);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Entry nonexistent not found in the container.');
        $container->get('nonexistent');
    }

    public function testGetNonInstantiableClass(): void
    {
        $container = new Container(true);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class PerfectApp\Tests\AbstractClass is not instantiable.');
        $container->get(AbstractClass::class);
    }

    public function testGetWithMissingRequiredParameter(): void
    {
        $container = new Container(true);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot resolve parameter $missingParam for PerfectApp\Tests\ClassWithRequiredParameter.');
        $container->get(ClassWithRequiredParameter::class);
    }

    public function testGetWithDefaultParameterValue(): void
    {
        $container = new Container(true);
        $instance = $container->get(ClassWithDefaultParameter::class);
        $this->assertInstanceOf(ClassWithDefaultParameter::class, $instance);
        $this->assertEquals(42, $instance->defaultParam);
    }

    public function testGetWithReflectionException(): void
    {
        $container = new Container(true);
        $nonExistentClass = 'NonExistentClass';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error while resolving $nonExistentClass");

        $container->get($nonExistentClass);
    }

    public function testHasReturnsTrueForRegisteredEntry(): void
    {
        $container = new Container(false);
        $container->set('test_key', 'test_value');

        $this->assertTrue($container->has('test_key'));
    }

    public function testHasReturnsFalseForUnregisteredEntry(): void
    {
        $container = new Container(false);

        $this->assertFalse($container->has('nonexistent_key'));
    }

    public function testGetWithClassNameString(): void
    {
        $container = new Container(false);
        $container->set('sample_class', SampleClass::class);

        $instance = $container->get('sample_class');
        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertEquals('Hello', $instance->prop);
    }
}
