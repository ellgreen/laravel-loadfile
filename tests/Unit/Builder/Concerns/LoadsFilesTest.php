<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\LoadsFiles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoadsFilesTest extends TestCase
{
    /** @var MockObject|LoadsFiles */
    private MockObject $loadsFiles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadsFiles = $this->getMockForTrait(LoadsFiles::class);
    }

    public function testSetFile()
    {
        $this->loadsFiles->file($path = '/path/to/file', true);

        $this->assertSame($path, $this->loadsFiles->getFile());
        $this->assertTrue($this->loadsFiles->isLocal());
    }

    public function testSetTable()
    {
        $this->loadsFiles->into($table = 'table');

        $this->assertSame($table, $this->loadsFiles->getTable());
    }

    public function testSetLocal()
    {
        $this->loadsFiles->local(true);

        $this->assertTrue($this->loadsFiles->isLocal());
    }

    public function testSetCharset()
    {
        $this->loadsFiles->charset($charset = 'utf8mb4');

        $this->assertSame($charset, $this->loadsFiles->getCharset());
    }
}
