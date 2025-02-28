<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasFile;
use PHPUnit\Framework\TestCase;

class HasFileTest extends TestCase
{
    /** @var HasFile */
    private $hasFile;

    protected function setUp(): void
    {
        $this->hasFile = new class
        {
            use HasFile;
        };
    }

    public function test_set_file(): void
    {
        $this->hasFile->file($path = '/path/to/file', true);

        $this->assertSame($path, $this->hasFile->getFile());
        $this->assertTrue($this->hasFile->isLocal());
    }

    public function test_set_table(): void
    {
        $this->hasFile->into($table = 'table');

        $this->assertSame($table, $this->hasFile->getTable());
    }

    public function test_set_local(): void
    {
        $this->hasFile->local(true);

        $this->assertTrue($this->hasFile->isLocal());
    }

    public function test_set_charset(): void
    {
        $this->hasFile->charset($charset = 'utf8mb4');

        $this->assertSame($charset, $this->hasFile->getCharset());
    }
}
