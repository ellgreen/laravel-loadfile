<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\ReplacesOrIgnores;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReplacesOrIgnoresTest extends TestCase
{
    /** @var MockObject|ReplacesOrIgnores */
    private MockObject $replacesOrIgnores;

    protected function setUp(): void
    {
        parent::setUp();

        $this->replacesOrIgnores = $this->getMockForTrait(ReplacesOrIgnores::class);
    }

    public function testSetReplace()
    {
        $this->replacesOrIgnores->ignore()->replace();

        $this->assertTrue($this->replacesOrIgnores->isReplace());
        $this->assertFalse($this->replacesOrIgnores->isIgnore());
    }

    public function testSetIgnore()
    {
        $this->replacesOrIgnores->replace()->ignore();

        $this->assertTrue($this->replacesOrIgnores->isIgnore());
        $this->assertFalse($this->replacesOrIgnores->isReplace());
    }
}
