<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasLines;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HasLinesTest extends TestCase
{
    /** @var MockObject|HasLines */
    private MockObject $hasLines;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasLines = $this->getMockForTrait(HasLines::class);
    }

    public function testSetLinesStartingBy()
    {
        $this->hasLines->linesStartingBy('');

        $this->assertSame('', $this->hasLines->getLinesStartingBy());
    }

    public function testSetLinesTerminatedBy()
    {
        $this->hasLines->linesTerminatedBy('\\n');

        $this->assertSame('\\n', $this->hasLines->getLinesTerminatedBy());
    }

    public function testSetLines()
    {
        $this->hasLines->lines('', '\\n');

        $this->assertSame('', $this->hasLines->getLinesStartingBy());
        $this->assertSame('\\n', $this->hasLines->getLinesTerminatedBy());
    }
}
