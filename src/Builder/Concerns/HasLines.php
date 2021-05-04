<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait HasLines
{
    private ?string $linesStartingBy = null;
    private ?string $linesTerminatedBy = null;

    public function linesStartingBy(?string $startingBy): self
    {
        $this->linesStartingBy = $startingBy;
        return $this;
    }

    public function linesTerminatedBy(?string $terminatedBy): self
    {
        $this->linesTerminatedBy = $terminatedBy;
        return $this;
    }

    public function lines(?string $startingBy, ?string $terminatedBy): self
    {
        $this->linesStartingBy($startingBy);
        $this->linesTerminatedBy($terminatedBy);

        return $this;
    }

    public function getLinesStartingBy(): ?string
    {
        return $this->linesStartingBy;
    }

    public function getLinesTerminatedBy(): ?string
    {
        return $this->linesTerminatedBy;
    }
}
