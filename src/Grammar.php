<?php

namespace EllGreen\LaravelLoadFile;

use EllGreen\LaravelLoadFile\Builder\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;

class Grammar extends MySqlGrammar
{
    public function compileLoadFile(Builder $query): array
    {
        $bindings = collect();
        $querySegments = collect();

        $querySegments->push(
            'load data' . ($query->isLocal() ? ' local' : ''),
            'infile ' . $this->quoteString($query->getFile()),
        );

        if ($query->isReplace()) {
            $querySegments->push('replace');
        }

        if ($query->isIgnore()) {
            $querySegments->push('ignore');
        }

        $querySegments->push('into table ' . $this->wrapTable($query->getTable()));

        if ($query->getCharset()) {
            $querySegments->push('character set ' . $this->quoteString($query->getCharset()));
        }

        $querySegments->push($this->compileFields(
            $query->getFieldsTerminatedBy(),
            $query->getFieldsEnclosedBy(),
            $query->getFieldsEscapedBy(),
            $query->getFieldsOptionallyEnclosed(),
        ));

        $querySegments->push($this->compileLines($query->getLinesStartingBy(), $query->getLinesTerminatedBy()));

        if ($query->getIgnoreLines() > 0) {
            $querySegments->push("ignore {$query->getIgnoreLines()} lines");
        }

        $columns = $query->getColumns();

        if (! empty($columns)) {
            $querySegments->push('(' . $this->columnize($query->getColumns()) . ')');
        }

        if ($set = $query->getSet()) {
            $querySegments->push($this->compileSetColumns($set));
            $bindings->push(...collect($set)->filter(fn($value) => ! $this->isExpression($value))->values());
        }

        return [$querySegments->filter()->implode(' '), $bindings->toArray()];
    }

    private function compileFields(
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
            isset($enclosedBy) ? ($optionallyEnclosed ? 'optionally' : '') : '',
            isset($enclosedBy) ? 'enclosed by ' . $this->quoteString($enclosedBy) : '',
            isset($escapedBy) ? 'escaped by ' . $this->quoteString($escapedBy) : '',
        ])->filter()->implode(' ');
    }

    private function compileLines(?string $startingBy, ?string $terminatedBy): string
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
