<?php

namespace EllGreen\LaravelLoadFile\Builder;

use EllGreen\LaravelLoadFile\CompiledQuery;
use EllGreen\LaravelLoadFile\Exceptions\CompilationException;
use EllGreen\LaravelLoadFile\Grammar;
use EllGreen\LaravelLoadFile\Builder\Concerns;
use Illuminate\Database\DatabaseManager;

class Builder
{
    use Concerns\HasFile;
    use Concerns\IsLowPriorityOrConcurrent;
    use Concerns\ReplacesOrIgnores;
    use Concerns\HasColumns;
    use Concerns\HasFields;
    use Concerns\HasLines;
    use Concerns\IgnoresLines;
    use Concerns\SetsValues;

    private DatabaseManager $databaseManager;
    private Grammar $grammar;

    private ?string $connection = null;

    public function __construct(DatabaseManager $databaseManager, Grammar $grammar)
    {
        $this->databaseManager = $databaseManager;
        $this->grammar = $grammar;
    }

    /**
     * @throws CompilationException
     */
    public function compile(): CompiledQuery
    {
        return $this->grammar->compileLoadFile($this);
    }

    /**
     * @throws CompilationException
     */
    public function load(): bool
    {
        $query = $this->compile();

        $connection = $this->databaseManager->connection($this->getConnectionName());
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
