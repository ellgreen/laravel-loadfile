<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasColumns;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HasColumnsTest extends TestCase
{
    public function testSetColumns()
    {
        /** @var MockObject|HasColumns $hasColumns */
        $hasColumns = $this->getMockForTrait(HasColumns::class);

        $hasColumns->columns($columns = ['column_1', 'column_2']);

        $this->assertSame($columns, $hasColumns->getColumns());
    }
}
