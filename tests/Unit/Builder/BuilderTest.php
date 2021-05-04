<?php

namespace Tests\Unit\Builder;

use EllGreen\LaravelLoadFile\Builder\Builder;
use EllGreen\LaravelLoadFile\CompiledQuery;
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

    public function testLoad()
    {
        $this->grammar->method('compileLoadFile')
            ->willReturn(new CompiledQuery('sql', ['bindings']));

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
