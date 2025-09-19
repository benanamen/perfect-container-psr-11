<?php
declare(strict_types=1);

namespace PerfectApp\Tests;

interface LoggerInterface
{
    public function log(string $message): void;
}
