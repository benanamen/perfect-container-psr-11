<?php declare(strict_types=1);

namespace PerfectApp\Tests;

use Exception;
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
        $container->bind('foo', 'bar');

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
    public function testGetWithBindAlias()
    {
        $container = new Container(false);
        $container->bind('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetNotFound()
    {
        $container = new Container(false);
        $this->expectException(Exception::class);
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
        $container = new Container(true); // Adjust the namespace and class name as needed
        $nonExistentClass = 'NonExistentClass';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error while resolving $nonExistentClass");

        $container->get($nonExistentClass);
    }
}
