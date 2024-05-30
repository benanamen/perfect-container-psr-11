<?php declare(strict_types=1);

namespace PerfectApp\Tests;

class ClassWithRequiredParameter
{
    public function __construct($missingParam)
    {
    }
}