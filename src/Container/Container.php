<?php declare(strict_types=1);

namespace PerfectApp\Container;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;

class Container implements ContainerInterface
{
    private array $entries = [];
    private mixed $autowiring;

    public function __construct($autowiring = false)
    {
        $this->autowiring = $autowiring;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function get($id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            return $entry instanceof Closure ? $entry($this) : $entry;
        }

        if ($this->autowiring) {
            $reflector = new ReflectionClass($id);
            if (!$reflector->isInstantiable()) {
                throw new Exception("Class $id is not instantiable.");
            }

            $constructor = $reflector->getConstructor();
            if ($constructor === null) {
                return new $id;
            }

            $parameters = [];
            foreach ($constructor->getParameters() as $parameter) {
                $class = $parameter->getType();
                if ($class === null) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $parameters[] = $parameter->getDefaultValue();
                    } else {
                        throw new Exception("Cannot resolve parameter \${$parameter->getName()} for $id.");
                    }
                } else {
                    $parameters[] = $this->get($class->getName());
                }
            }

            return $reflector->newInstanceArgs($parameters);
        }

        throw new Exception("Entry $id not found in the container.");
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    public function set($id, $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    public function bind($id, $concrete): void
    {
        $this->set($id, $concrete);
    }
}
