<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait HasColumns
{
    private ?array $columns = null;

    public function columns(?array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function getColumns(): ?array
    {
        return $this->columns;
    }
}
