<?php declare(strict_types=1);

namespace PerfectApp\Tests;

class ServiceClass
{
    public SampleClass $dependency;

    public function __construct(SampleClass $dependency)
    {
        $this->dependency = $dependency;
    }
}