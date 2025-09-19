<?php
declare(strict_types=1);

namespace PerfectApp\Tests;

class ClassWithUnionType
{
    private string|int $param;

    public function __construct(string|int $param)
    {
        $this->param = $param;
    }

    public function getParam(): string|int
    {
        return $this->param;
    }
}
