<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\ReplacesOrIgnores;
use PHPUnit\Framework\TestCase;

class ReplacesOrIgnoresTest extends TestCase
{
    /** @var ReplacesOrIgnores */
    private $replacesOrIgnores;

    protected function setUp(): void
    {
        $this->replacesOrIgnores = new class
        {
            use ReplacesOrIgnores;
        };
    }

    public function test_set_replace(): void
    {
        $this->replacesOrIgnores->ignore()->replace();

        $this->assertTrue($this->replacesOrIgnores->isReplace());
        $this->assertFalse($this->replacesOrIgnores->isIgnore());
    }

    public function test_set_ignore(): void
    {
        $this->replacesOrIgnores->replace()->ignore();

        $this->assertTrue($this->replacesOrIgnores->isIgnore());
        $this->assertFalse($this->replacesOrIgnores->isReplace());
    }
}
