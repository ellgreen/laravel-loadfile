<?php

namespace EllGreen\LaravelLoadFile\Builder;

use EllGreen\LaravelLoadFile\Grammar;
use EllGreen\LaravelLoadFile\Builder\Concerns;
use Illuminate\Database\DatabaseManager;

class Builder
{
    use Concerns\LoadsFiles;
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

    public function compile(): array
    {
        return $this->grammar->compileLoadFile($this);
    }

    public function load(): bool
    {
        list($sql, $bindings) = $this->compile();

        $connection = $this->databaseManager->connection($this->connection);
        return $connection->statement($sql, $bindings);
    }

    public function connection(?string $name = null): self
    {
        $this->connection = $name;
        return $this;
    }
}
