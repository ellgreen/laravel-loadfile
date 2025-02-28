<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\IgnoresLines;
use PHPUnit\Framework\TestCase;

class IgnoresLinesTest extends TestCase
{
    /** @var IgnoresLines */
    private $ignoresLines;

    protected function setUp(): void
    {
        $this->ignoresLines = new class
        {
            use IgnoresLines;
        };
    }

    public function test_set_ignore_lines(): void
    {
        $this->ignoresLines->ignoreLines(1);

        $this->assertSame(1, $this->ignoresLines->getIgnoreLines());
    }
}
