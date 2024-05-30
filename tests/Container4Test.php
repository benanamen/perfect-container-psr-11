<?php declare(strict_types=1);


namespace PerfectApp\Tests;


use Exception;
use PerfectApp\Container\Container4;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(Container4::class)]
class Container4Test extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithoutAutowiring()
    {
        $container = new Container4(false);
        $container->bind('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetWithAutowiring()
    {
        $container = new Container4(true);

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
        $container = new Container4(true);

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
        $container = new Container4(false);
        $container->bind('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetNotFound()
    {
        $container = new Container4(false);
        $this->expectException(Exception::class);
        $container->get('nonexistent');
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetNonInstantiableClass()
    {
        $container = new Container4(true);
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
        $container = new Container4(true);
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
        $container = new Container4(true);
        $instance = $container->get(ClassWithDefaultParameter::class);
        $this->assertInstanceOf(ClassWithDefaultParameter::class, $instance);
        $this->assertEquals(42, $instance->defaultParam);
    }

    public function testGetWithReflectionException()
    {
        $container = new Container4(true); // Adjust the namespace and class name as needed
        $nonExistentClass = 'NonExistentClass';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error while resolving $nonExistentClass");

        $container->get($nonExistentClass);
    }
}

class SampleClass
{
    public string $prop = 'Hello';
}

class ServiceClass
{
    public SampleClass $dependency;

    public function __construct(SampleClass $dependency)
    {
        $this->dependency = $dependency;
    }
}

abstract class AbstractClass
{
}

class ClassWithRequiredParameter
{
    public function __construct($missingParam)
    {
    }
}

class ClassWithDefaultParameter
{
    public mixed $defaultParam;

    public function __construct($defaultParam = 42)
    {
        $this->defaultParam = $defaultParam;
    }
}