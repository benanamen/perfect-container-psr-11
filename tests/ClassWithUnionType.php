<?php
declare(strict_types=1);


namespace PerfectApp\Tests;

class ClassWithUnionType
{
    public function __construct(string|int $param)
    {
    }
}
