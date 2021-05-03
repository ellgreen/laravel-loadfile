<?php

namespace EllGreen\LaravelLoadFile;

use Illuminate\Database\Query\Grammars\MySqlGrammar;

class Grammar extends MySqlGrammar
{
    public function compileLoadFile(Builder $query): array
    {
        $bindings = collect();
        $querySegments = collect();

        $querySegments->push(
            'load data' . ($query->local ? ' local' : ''),
            'infile ' . $this->quoteString($query->file),
        );

        if ($query->replace) {
            $querySegments->push('replace');
        }

        if ($query->ignore) {
            $querySegments->push('ignore');
        }

        $querySegments->push('into table ' . $this->wrapTable($query->table));

        if (isset($query->charset)) {
            $querySegments->push('character set ' . $this->quoteString($query->charset));
        }

        $querySegments->push($this->compileFields(
            $query->fieldsTerminatedBy,
            $query->fieldsEnclosedBy,
            $query->fieldsEscapedBy,
            $query->fieldsOptionallyEnclosed,
        ));

        $querySegments->push($this->compileLines($query->linesStartingBy, $query->linesTerminatedBy));

        if ($query->ignoreLines > 0) {
            $querySegments->push("ignore {$query->ignoreLines} lines");
        }

        $querySegments->push('(' . $this->columnize($query->columns) . ')');

        if (isset($query->set)) {
            $querySegments->push($this->compileSetColumns($query->set));
            $bindings->push(...collect($query->set)->filter(fn($value) => ! $this->isExpression($value))->values());
        }

        return [$querySegments->filter()->implode(' '), $bindings->toArray()];
    }

    public function compileFields(
        ?string $terminatedBy,
        ?string $enclosedBy,
        ?string $escapedBy,
        bool $optionallyEnclosed
    ): string {
        if (! isset($terminatedBy) && ! isset($enclosedBy) && ! isset($escapedBy)) {
            return '';
        }

        return collect([
            'fields',
            isset($terminatedBy) ? 'terminated by ' . $this->quoteString($terminatedBy) : '',
            $enclosedBy ? ($optionallyEnclosed ? 'optionally' : '') : '',
            isset($enclosedBy) ? 'enclosed by ' . $this->quoteString($enclosedBy) : '',
            isset($escapedBy) ? 'escaped by ' . $this->quoteString($escapedBy) : '',
        ])->filter()->implode(' ');
    }

    public function compileLines(?string $startingBy, ?string $terminatedBy): string
    {
        if (! isset($startingBy) && ! isset($terminatedBy)) {
            return '';
        }

        return collect([
            'lines',
            isset($startingBy) ? 'starting by ' . $this->quoteString($startingBy) : '',
            isset($terminatedBy) ? 'terminated by ' . $this->quoteString($terminatedBy) : '',
        ])->filter()->implode(' ');
    }

    private function compileSetColumns(array $values): string
    {
        return 'set ' . collect($values)->map(function ($value, $key) {
            return $this->wrap($key) . ' = ' . $this->parameter($value);
        })->implode(', ');
    }
}
