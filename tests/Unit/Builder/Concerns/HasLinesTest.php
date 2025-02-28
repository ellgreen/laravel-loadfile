<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasLines;
use PHPUnit\Framework\TestCase;

class HasLinesTest extends TestCase
{
    /** @var HasLines */
    private $hasLines;

    protected function setUp(): void
    {
        $this->hasLines = new class
        {
            use HasLines;
        };
    }

    public function test_set_lines_starting_by(): void
    {
        $this->hasLines->linesStartingBy('');

        $this->assertSame('', $this->hasLines->getLinesStartingBy());
    }

    public function test_set_lines_terminated_by(): void
    {
        $this->hasLines->linesTerminatedBy('\\n');

        $this->assertSame('\\n', $this->hasLines->getLinesTerminatedBy());
    }

    public function test_set_lines(): void
    {
        $this->hasLines->lines('', '\\n');

        $this->assertSame('', $this->hasLines->getLinesStartingBy());
        $this->assertSame('\\n', $this->hasLines->getLinesTerminatedBy());
    }
}
