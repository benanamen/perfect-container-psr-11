<?php

declare(strict_types=1);

namespace PerfectApp\Container;

use Closure;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Throwable;

/**
 * PSR-11 Container implementation with autowiring support
 */
class Container implements PsrContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $entries = [];

    private bool $autowiring;

    public function __construct(bool $autowiring = true)
    {
        $this->autowiring = $autowiring;
    }

    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];

            // 1. If it's a Closure (factory), call it
            if ($entry instanceof Closure) {
                return $entry($this);
            } // 2. If it's a string that represents a valid class, build it
            elseif (is_string($entry) && class_exists($entry)) {
                return $this->build($entry);
            } // 3. Return anything else as-is (objects, simple values, etc.)
            else {
                return $entry;
            }
        }

        if ($this->autowiring) {
            return $this->build($id);
        }

        throw new class ("Entry $id not found in the container.") extends Exception implements
            NotFoundExceptionInterface {
            public function __construct(string $message)
            {
                parent::__construct($message);
            }
        };
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, mixed $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    /**
     * @param string $id
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function build(string $id): object
    {
        try {
            // Check if it's a valid class string before reflection
            if (!class_exists($id)) {
                // This will be caught by the ReflectionException catch block
                throw new ReflectionException("Class $id does not exist");
            }

            $reflector = new ReflectionClass($id);

            if (!$reflector->isInstantiable()) {
                throw new class ("Class $id is not instantiable.") extends Exception implements
                    ContainerExceptionInterface {
                    public function __construct(string $message)
                    {
                        parent::__construct($message);
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
                if ($type === null || ($type instanceof ReflectionNamedType && $type->isBuiltin())) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $parameters[] = $parameter->getDefaultValue();
                    } else {
                        throw new class ("Cannot resolve parameter \${$parameter->getName()} for $id.") extends Exception implements ContainerExceptionInterface {
                            public function __construct(string $message)
                            {
                                parent::__construct($message);
                            }
                        };
                    }
                } else {
                    if ($type instanceof ReflectionNamedType) {
                        $parameters[] = $this->get($type->getName());
                    } else {
                        throw new class ("Union types are not supported for parameter \${$parameter->getName()} in $id.") extends Exception implements ContainerExceptionInterface {
                            public function __construct(string $message)
                            {
                                parent::__construct($message);
                            }
                        };
                    }
                }
            }

            return $reflector->newInstanceArgs($parameters);
        } catch (ReflectionException $e) {
            // This will catch ReflectionException from invalid class names and other reflection issues
            throw new class ("Error while resolving $id", 0, $e) extends Exception implements
                ContainerExceptionInterface {
                public function __construct(string $message, int $code, Throwable $previous)
                {
                    parent::__construct($message, $code, $previous);
                }
            };
        }
    }
}
