<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\IgnoresLines;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IgnoresLinesTest extends TestCase
{
    /** @var MockObject|IgnoresLines */
    private MockObject $ignoresLines;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ignoresLines = $this->getMockForTrait(IgnoresLines::class);
    }

    public function testSetIgnoreLines()
    {
        $this->ignoresLines->ignoreLines(1);

        $this->assertSame(1, $this->ignoresLines->getIgnoreLines());
    }
}
