<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait IsLowPriorityOrConcurrent
{
    private bool $lowPriority = false;
    private bool $concurrent = false;

    public function lowPriority(bool $lowPriority = true): self
    {
        $this->lowPriority = $lowPriority;

        if ($lowPriority) {
            $this->concurrent = false;
        }

        return $this;
    }

    public function concurrent(bool $concurrent = true): self
    {
        $this->concurrent = $concurrent;

        if ($concurrent) {
            $this->lowPriority = false;
        }

        return $this;
    }

    public function isLowPriority(): bool
    {
        return $this->lowPriority;
    }

    public function isConcurrent(): bool
    {
        return $this->concurrent;
    }
}
