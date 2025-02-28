<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasColumns;
use PHPUnit\Framework\TestCase;

class HasColumnsTest extends TestCase
{
    public function test_set_columns(): void
    {
        $hasColumns = new class
        {
            use HasColumns;
        };

        $hasColumns->columns($columns = ['column_1', 'column_2']);

        $this->assertSame($columns, $hasColumns->getColumns());
    }
}
