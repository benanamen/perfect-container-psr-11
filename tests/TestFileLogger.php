<?php
declare(strict_types=1);

namespace PerfectApp\Tests;

class TestFileLogger implements LoggerInterface
{
    public function log(string $message): string
    {
        // Simple implementation for testing
        return "Logged: $message";
    }
}
