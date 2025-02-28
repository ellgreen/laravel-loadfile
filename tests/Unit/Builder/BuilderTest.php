<?php

namespace Tests\Unit\Builder;

use EllGreen\LaravelLoadFile\Builder\Builder;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new Builder(
            $this->databaseManager = $this->createMock(DatabaseManager::class),
        );
    }

    public function test_load(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('statement')
            ->with('load data infile \'abc.csv\' into table `test`', [])
            ->willReturn(true);

        $this->databaseManager->method('connection')
            ->with('mysql')
            ->willReturn($connection);

        $this->assertTrue(
            $this->builder->file('abc.csv')->into('test')->connection('mysql')->load()
        );
    }
}
