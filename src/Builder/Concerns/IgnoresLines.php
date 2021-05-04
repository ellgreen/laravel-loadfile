<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait IgnoresLines
{
    private int $ignoreLines = 0;

    public function ignoreLines(int $count): self
    {
        $this->ignoreLines = $count;
        return $this;
    }

    public function getIgnoreLines(): int
    {
        return $this->ignoreLines;
    }
}
