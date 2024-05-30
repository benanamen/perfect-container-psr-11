<?php declare(strict_types=1);
//Version 4.1 Working in Perfect Storage - Was missing Closure import
namespace PerfectApp\Container;

use Closure;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;

class Container4 implements PsrContainerInterface
{
    private array $entries = [];
    private bool $autowiring;

    public function __construct(bool $autowiring = true)
    {
        $this->autowiring = $autowiring;
    }

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            return $entry instanceof Closure ? $entry($this) : $entry;
        }

        if ($this->autowiring) {
            return $this->build($id);
        }

        throw new class($id) extends Exception implements NotFoundExceptionInterface {
            public function __construct($id)
            {
                parent::__construct("Entry $id not found in the container.");
            }
        };
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function bind(string $id, $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function build(string $id)
    {
        try {
            $reflector = new ReflectionClass($id);
            if (!$reflector->isInstantiable()) {
                throw new class($id) extends Exception implements ContainerExceptionInterface {
                    public function __construct($id)
                    {
                        parent::__construct("Class $id is not instantiable.");
                    }
                };
            }

            $constructor = $reflector->getConstructor();
            if ($constructor === null) {
                return new $id();
            }

            $parameters = [];
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type === null || $type->isBuiltin()) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $parameters[] = $parameter->getDefaultValue();
                    } else {
                        throw new class($id, $parameter) extends Exception implements ContainerExceptionInterface {
                            public function __construct($id, $parameter)
                            {
                                parent::__construct("Cannot resolve parameter \${$parameter->getName()} for $id.");
                            }
                        };
                    }
                } else {
                    $parameters[] = $this->get($type->getName());
                }
            }

            return $reflector->newInstanceArgs($parameters);
        } catch (ReflectionException $e) {
            throw new class($id, $e) extends Exception implements ContainerExceptionInterface {
                public function __construct($id, $previous)
                {
                    parent::__construct("Error while resolving $id: " . $previous->getMessage(), 0, $previous);
                }
            };
        }
    }
}
