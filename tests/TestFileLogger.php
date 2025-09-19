<?php

declare(strict_types=1);

namespace PerfectApp\Tests;

class TestFileLogger implements LoggerInterface
{
    public function log(string $message): void
    {
        file_put_contents('test.log', $message . PHP_EOL, FILE_APPEND);
    }
}
