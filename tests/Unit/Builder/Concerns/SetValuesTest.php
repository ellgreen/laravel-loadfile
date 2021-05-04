<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\SetsValues;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SetValuesTest extends TestCase
{
    /** @var MockObject|SetsValues */
    private MockObject $setsValues;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setsValues = $this->getMockForTrait(SetsValues::class);
    }

    public function testSetSet()
    {
        $this->setsValues->set($set = ['column_1' => 'value', 'column_2' => 123]);

        $this->assertSame($set, $this->setsValues->getSet());
    }
}
