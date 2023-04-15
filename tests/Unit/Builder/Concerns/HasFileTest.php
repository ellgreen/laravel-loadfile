<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasFile;
use EllGreen\LaravelLoadFile\Builder\FileType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HasFileTest extends TestCase
{
    /** @var MockObject|HasFile */
    private MockObject $loadsFiles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadsFiles = $this->getMockForTrait(HasFile::class);
    }

    public function testSetFile()
    {
        $this->loadsFiles->file($path = '/path/to/file', true);

        $this->assertSame($path, $this->loadsFiles->getFile());
        $this->assertTrue($this->loadsFiles->isLocal());
        $this->assertSame(FileType::CSV, $this->loadsFiles->getFileType());
    }

    public function testSetXmlFile()
    {
        $this->loadsFiles->xml($path = '/path/to/file', true);

        $this->assertSame($path, $this->loadsFiles->getFile());
        $this->assertTrue($this->loadsFiles->isLocal());
        $this->assertSame(FileType::XML, $this->loadsFiles->getFileType());
    }

    public function testSetFileType()
    {
        $this->loadsFiles->fileType(FileType::XML);

        $this->assertSame(FileType::XML, $this->loadsFiles->getFileType());
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
