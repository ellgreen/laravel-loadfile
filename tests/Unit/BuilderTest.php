<?php

namespace Tests\Unit;

use EllGreen\LaravelLoadFile\Builder;
use EllGreen\LaravelLoadFile\Grammar;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    private Builder $builder;
    /**
     * @var DatabaseManager|MockObject
     */
    private $databaseManager;
    /**
     * @var Grammar|MockObject
     */
    private $grammar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new Builder(
            $this->databaseManager = $this->createMock(DatabaseManager::class),
            $this->grammar = $this->createMock(Grammar::class),
        );
    }

    public function testSetFile()
    {
        $this->builder->file($path = '/path/to/file', true);

        $this->assertSame($path, $this->builder->file);
        $this->assertTrue($this->builder->local);
    }

    public function testSetTable()
    {
        $this->builder->into($table = 'table');

        $this->assertSame($table, $this->builder->table);
    }

    public function testSetLocal()
    {
        $this->builder->local(true);

        $this->assertTrue($this->builder->local);
    }

    public function testSetCharset()
    {
        $this->builder->charset($charset = 'utf8mb4');

        $this->assertSame($charset, $this->builder->charset);
    }

    public function testSetFieldsTerminatedBy()
    {
        $this->builder->fieldsTerminatedBy(',');

        $this->assertSame(',', $this->builder->fieldsTerminatedBy);
    }

    public function testSetFieldsEnclosedBy()
    {
        $this->builder->fieldsEnclosedBy('"', $optionally = true);

        $this->assertSame('"', $this->builder->fieldsEnclosedBy);
        $this->assertTrue($this->builder->fieldsOptionallyEnclosed);
    }

    public function testSetFieldsEscapedBy()
    {
        $this->builder->fieldsEscapedBy('\\\\');

        $this->assertSame('\\\\', $this->builder->fieldsEscapedBy);
    }

    public function testSetFields()
    {
        $this->builder->fields(',','"', '\\\\', true);

        $this->assertSame(',', $this->builder->fieldsTerminatedBy);
        $this->assertSame('"', $this->builder->fieldsEnclosedBy);
        $this->assertSame('\\\\', $this->builder->fieldsEscapedBy);
        $this->assertTrue($this->builder->fieldsOptionallyEnclosed);
    }

    public function testSetLinesStartingBy()
    {
        $this->builder->linesStartingBy('');

        $this->assertSame('', $this->builder->linesStartingBy);
    }

    public function testSetLinesTerminatedBy()
    {
        $this->builder->linesTerminatedBy('\\n');

        $this->assertSame('\\n', $this->builder->linesTerminatedBy);
    }

    public function testSetLines()
    {
        $this->builder->lines('', '\\n');

        $this->assertSame('', $this->builder->linesStartingBy);
        $this->assertSame('\\n', $this->builder->linesTerminatedBy);
    }

    public function testSetIgnore()
    {
        $this->builder->ignore(1);
        
        $this->assertSame(1, $this->builder->ignoreLines);
    }

    public function testSetColumns()
    {
        $this->builder->columns($columns = ['column_1', 'column_2']);

        $this->assertSame($columns, $this->builder->columns);
    }

    public function testSetSet()
    {
        $this->builder->set($set = ['column_1' => 'value', 'column_2' => 123]);

        $this->assertSame($set, $this->builder->set);
    }

    public function testLoad()
    {
        $this->grammar->method('compileLoadFile')
            ->willReturn(['sql', ['bindings']]);

        $connection = $this->createMock(Connection::class);
        $connection->method('statement')
            ->with('sql', ['bindings'])
            ->willReturn(true);

        $this->databaseManager->method('connection')
            ->with('mysql')
            ->willReturn($connection);

        $this->assertTrue(
            $this->builder->connection('mysql')->load()
        );
    }
}
