<?php

namespace EllGreen\LaravelLoadFile;

use Illuminate\Database\DatabaseManager;

class Builder
{
    private Grammar $grammar;
    private DatabaseManager $databaseManager;

    private ?string $connection = null;

    public ?string $file = null;
    public ?string $table = null;
    public bool $local = false;
    public ?string $charset = null;

    public ?string $fieldsTerminatedBy = null;
    public ?string $fieldsEnclosedBy = null;
    public bool $fieldsOptionallyEnclosed = false;
    public ?string $fieldsEscapedBy = null;
    public ?string $linesStartingBy = null;
    public ?string $linesTerminatedBy = null;

    public int $ignoreLines = 0;
    public array $columns = [];
    public ?array $set = null;

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

    public function file(string $file, ?bool $local = null): self
    {
        $this->file = $file;

        if (isset($local)) {
            $this->local($local);
        }

        return $this;
    }

    public function into(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function local(bool $local): self
    {
        $this->local = $local;
        return $this;
    }

    public function charset(?string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    public function fieldsTerminatedBy(string $terminatedBy): self
    {
        $this->fieldsTerminatedBy = $terminatedBy;
        return $this;
    }

    public function fieldsEnclosedBy(string $enclosedBy, ?bool $optionally = null): self
    {
        $this->fieldsEnclosedBy = $enclosedBy;

        if (isset($optionally)) {
            $this->fieldsOptionallyEnclosed($optionally);
        }

        return $this;
    }

    public function fieldsOptionallyEnclosed(bool $optionallyEnclosed): self
    {
        $this->fieldsOptionallyEnclosed = $optionallyEnclosed;
        return $this;
    }

    public function fieldsEscapedBy($escapedBy): self
    {
        $this->fieldsEscapedBy = $escapedBy;
        return $this;
    }

    public function fields(
        string $terminatedBy,
        string $enclosedBy,
        string $escapedBy,
        ?bool $optionallyEnclosed = null
    ): self {
        $this->fieldsTerminatedBy($terminatedBy);
        $this->fieldsEnclosedBy($enclosedBy, $optionallyEnclosed);
        $this->fieldsEscapedBy($escapedBy);

        return $this;
    }

    public function linesStartingBy(string $startingBy): self
    {
        $this->linesStartingBy = $startingBy;
        return $this;
    }

    public function linesTerminatedBy(string $terminatedBy): self
    {
        $this->linesTerminatedBy = $terminatedBy;
        return $this;
    }

    public function lines(string $startingBy, string $terminatedBy): self
    {
        $this->linesStartingBy($startingBy);
        $this->linesTerminatedBy($terminatedBy);

        return $this;
    }

    public function ignore(int $lines): self
    {
        $this->ignoreLines = $lines;
        return $this;
    }

    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function set(?array $set): self
    {
        $this->set = $set;
        return $this;
    }
}
