<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\IsLowPriorityOrConcurrent;
use PHPUnit\Framework\TestCase;

class IsLowPriorityOrConcurrentTest extends TestCase
{
    /** @var IsLowPriorityOrConcurrent */
    private $isLowPriorityOrConcurrent;

    protected function setUp(): void
    {
        $this->isLowPriorityOrConcurrent = new class
        {
            use IsLowPriorityOrConcurrent;
        };
    }

    public function test_set_low_priority(): void
    {
        $this->isLowPriorityOrConcurrent->concurrent()->lowPriority();

        $this->assertTrue($this->isLowPriorityOrConcurrent->isLowPriority());
        $this->assertFalse($this->isLowPriorityOrConcurrent->isConcurrent());
    }

    public function test_set_ignore(): void
    {
        $this->isLowPriorityOrConcurrent->lowPriority()->concurrent();

        $this->assertTrue($this->isLowPriorityOrConcurrent->isConcurrent());
        $this->assertFalse($this->isLowPriorityOrConcurrent->isLowPriority());
    }
}
