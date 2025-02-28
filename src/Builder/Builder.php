<?php

namespace EllGreen\LaravelLoadFile\Builder;

use EllGreen\LaravelLoadFile\CompiledQuery;
use EllGreen\LaravelLoadFile\Exceptions\CompilationException;
use EllGreen\LaravelLoadFile\Grammar;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

class Builder
{
    use Concerns\HasColumns;
    use Concerns\HasFields;
    use Concerns\HasFile;
    use Concerns\HasLines;
    use Concerns\IgnoresLines;
    use Concerns\IsLowPriorityOrConcurrent;
    use Concerns\ReplacesOrIgnores;
    use Concerns\SetsValues;

    private DatabaseManager $databaseManager;

    private ?string $connection = null;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @throws CompilationException
     */
    public function compile(Connection $connection): CompiledQuery
    {
        $grammar = new Grammar($connection);

        return $grammar->compileLoadFile($this);
    }

    /**
     * @throws CompilationException
     */
    public function load(): bool
    {
        $connection = $this->databaseManager->connection($this->getConnectionName());

        $query = $this->compile($connection);

        return $connection->statement($query->getSql(), $query->getBindings());
    }

    public function connection(?string $name = null): self
    {
        $this->connection = $name;

        return $this;
    }

    public function getConnectionName(): ?string
    {
        return $this->connection;
    }
}
