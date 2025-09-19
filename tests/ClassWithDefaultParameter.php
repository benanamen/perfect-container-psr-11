<?php declare(strict_types=1);

namespace PerfectApp\Tests;

class ClassWithDefaultParameter
{
    public mixed $defaultParam;

    public function __construct(mixed $defaultParam = 42)
    {
        $this->defaultParam = $defaultParam;
    }
}
