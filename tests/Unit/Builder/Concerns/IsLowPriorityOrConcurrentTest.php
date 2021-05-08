<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\IsLowPriorityOrConcurrent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IsLowPriorityOrConcurrentTest extends TestCase
{
    /** @var MockObject|IsLowPriorityOrConcurrent */
    private MockObject $isLowPriorityOrConcurrent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isLowPriorityOrConcurrent = $this->getMockForTrait(IsLowPriorityOrConcurrent::class);
    }

    public function testSetLowPriority(): void
    {
        $this->isLowPriorityOrConcurrent->concurrent()->lowPriority();

        $this->assertTrue($this->isLowPriorityOrConcurrent->isLowPriority());
        $this->assertFalse($this->isLowPriorityOrConcurrent->isConcurrent());
    }

    public function testSetIgnore(): void
    {
        $this->isLowPriorityOrConcurrent->lowPriority()->concurrent();

        $this->assertTrue($this->isLowPriorityOrConcurrent->isConcurrent());
        $this->assertFalse($this->isLowPriorityOrConcurrent->isLowPriority());
    }
}
