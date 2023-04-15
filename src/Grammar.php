<?php

namespace EllGreen\LaravelLoadFile;

use EllGreen\LaravelLoadFile\Builder\Builder;
use EllGreen\LaravelLoadFile\Builder\FileType;
use EllGreen\LaravelLoadFile\Exceptions\CompilationException;
use Illuminate\Database\Query\Grammars\MySqlGrammar;

class Grammar extends MySqlGrammar
{
    /**
     * @throws CompilationException
     */
    public function compileLoadFile(Builder $query): CompiledQuery
    {
        $bindings = collect();
        $querySegments = collect();

        $file = $query->getFile();
        $table = $query->getTable();

        if (! isset($file, $table)) {
            throw CompilationException::noFileOrTableSupplied();
        }

        $querySegments->push('load ' . $query->getFileType()->value);

        if ($query->isLowPriority()) {
            $querySegments->push('low_priority');
        }

        if ($query->isConcurrent()) {
            $querySegments->push('concurrent');
        }

        if ($query->isLocal()) {
            $querySegments->push('local');
        }

        $querySegments->push('infile ' . $this->quoteString($file));

        if ($query->isReplace()) {
            $querySegments->push('replace');
        }

        if ($query->isIgnore()) {
            $querySegments->push('ignore');
        }

        $querySegments->push('into table ' . $this->wrapTable($table));

        if ($charset = $query->getCharset()) {
            $querySegments->push('character set ' . $this->quoteString($charset));
        }

        if ($query->getFileType() !== FileType::XML) {
            $querySegments->push($this->compileFields(
                $query->getFieldsTerminatedBy(),
                $query->getFieldsEnclosedBy(),
                $query->getFieldsEscapedBy(),
                $query->getFieldsOptionallyEnclosed(),
            ));

            $querySegments->push($this->compileLines($query->getLinesStartingBy(), $query->getLinesTerminatedBy()));
        }

        if ($query->getFileType() === FileType::XML && ! empty($query->getRowsIdentifiedBy())) {
            $querySegments->push('rows identified by ' . $this->quoteString($query->getRowsIdentifiedBy()));
        }

        if ($query->getIgnoreLines() > 0) {
            $querySegments->push("ignore {$query->getIgnoreLines()} lines");
        }

        $columns = $query->getColumns();

        if (! empty($columns)) {
            $querySegments->push('(' . $this->columnize($columns) . ')');
        }

        if ($set = $query->getSet()) {
            $querySegments->push($this->compileSetColumns($set));
            /** @psalm-suppress MissingClosureParamType|InvalidArgument */
            $values = collect($set)->filter(fn($value) => ! $this->isExpression($value))->values();
            $bindings->push($values->toArray());
        }

        return new CompiledQuery(
            $querySegments->flatten()->filter()->implode(' '),
            $bindings->flatten()->toArray()
        );
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
        /** @psalm-suppress MissingClosureParamType */
        return 'set ' . collect($values)->map(function ($value, $key) {
            /** @psalm-suppress MixedArgument */
            return $this->wrap($key) . ' = ' . $this->parameter($value);
        })->implode(', ');
    }
}
