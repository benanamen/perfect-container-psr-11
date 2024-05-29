<?php declare(strict_types=1);


namespace PerfectApp\Tests;


use Exception;

use PerfectApp\Container\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionException;

#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testGetWithoutAutowiring()
    {
        $container = new Container(false);
        $container->set('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @throws ReflectionException
     */
    public function testGetWithAutowiring()
    {
        $container = new Container(true);

        $instance = $container->get(SampleClass::class);
        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertEquals('Hello', $instance->prop);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetWithDependencyAutowiring()
    {
        $container = new Container(true);

        $service = $container->get(ServiceClass::class);
        $this->assertInstanceOf(ServiceClass::class, $service);
        $this->assertInstanceOf(SampleClass::class, $service->dependency);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetWithBindAlias()
    {
        $container = new Container(false);
        $container->bind('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @throws ReflectionException
     */
    public function testGetNotFound()
    {
        $container = new Container(false);
        $this->expectException(Exception::class);
        $container->get('nonexistent');
    }



    public function testGetNonInstantiableClass()
    {
        $container = new Container(true);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class PerfectApp\Tests\AbstractClass is not instantiable.');
        $container->get(AbstractClass::class);
    }

    public function testGetWithMissingRequiredParameter()
    {
        $container = new Container(true);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot resolve parameter $missingParam for PerfectApp\Tests\ClassWithRequiredParameter.');
        $container->get(ClassWithRequiredParameter::class);
    }

    public function testGetWithDefaultParameterValue()
    {
        $container = new Container(true);
        $instance = $container->get(ClassWithDefaultParameter::class);
        $this->assertInstanceOf(ClassWithDefaultParameter::class, $instance);
        $this->assertEquals(42, $instance->defaultParam);
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
    public $defaultParam;

    public function __construct($defaultParam = 42)
    {
        $this->defaultParam = $defaultParam;
    }
}