<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\SetsValues;
use PHPUnit\Framework\TestCase;

class SetValuesTest extends TestCase
{
    /** @var SetsValues */
    private $setsValues;

    protected function setUp(): void
    {
        $this->setsValues = new class
        {
            use SetsValues;
        };
    }

    public function test_set_set(): void
    {
        $this->setsValues->set($set = ['column_1' => 'value', 'column_2' => 123]);

        $this->assertSame($set, $this->setsValues->getSet());
    }
}
