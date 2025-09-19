<?php declare(strict_types=1);

namespace PerfectApp\Tests;

use Exception;
use FileLogger;
use LoggerInterface;
use PerfectApp\Container\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithoutAutowiring()
    {
        $container = new Container(false);
        $container->set('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithAutowiring()
    {
        $container = new Container(true);

        $instance = $container->get(SampleClass::class);
        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertEquals('Hello', $instance->prop);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithDependencyAutowiring()
    {
        $container = new Container(true);

        $service = $container->get(ServiceClass::class);
        $this->assertInstanceOf(ServiceClass::class, $service);
        $this->assertInstanceOf(SampleClass::class, $service->dependency);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithInterfaceBinding()
    {
        $container = new Container(true);
        $container->set(LoggerInterface::class, TestFileLogger::class);

        $logger = $container->get(LoggerInterface::class);
        $this->assertInstanceOf(TestFileLogger::class, $logger);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithStringValue()
    {
        $container = new Container(false);
        $container->set('version', '1.0.0');

        $this->assertEquals('1.0.0', $container->get('version'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithClosureFactory()
    {
        $container = new Container(false);
        $container->set('timestamp', function () {
            return time();
        });

        $timestamp = $container->get('timestamp');
        $this->assertIsInt($timestamp);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetNotFound()
    {
        $container = new Container(false);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Entry nonexistent not found in the container.');
        $container->get('nonexistent');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetNonInstantiableClass()
    {
        $container = new Container(true);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class PerfectApp\Tests\AbstractClass is not instantiable.');
        $container->get(AbstractClass::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithMissingRequiredParameter()
    {
        $container = new Container(true);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot resolve parameter $missingParam for PerfectApp\Tests\ClassWithRequiredParameter.');
        $container->get(ClassWithRequiredParameter::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithDefaultParameterValue()
    {
        $container = new Container(true);
        $instance = $container->get(ClassWithDefaultParameter::class);
        $this->assertInstanceOf(ClassWithDefaultParameter::class, $instance);
        $this->assertEquals(42, $instance->defaultParam);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithReflectionException()
    {
        $container = new Container(true);
        $nonExistentClass = 'NonExistentClass';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error while resolving $nonExistentClass");

        $container->get($nonExistentClass);
    }

    /**
     */
    public function testHasReturnsTrueForRegisteredEntry()
    {
        $container = new Container(false);
        $container->set('test_key', 'test_value');

        $this->assertTrue($container->has('test_key'));
    }

    /**
     */
    public function testHasReturnsFalseForUnregisteredEntry()
    {
        $container = new Container(false);

        $this->assertFalse($container->has('nonexistent_key'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithClassNameString()
    {
        $container = new Container(false);

        // This tests the new logic: is_string($entry) && class_exists($entry)
        $container->set('sample_class', SampleClass::class);

        $instance = $container->get('sample_class');
        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertEquals('Hello', $instance->prop);
    }
}
