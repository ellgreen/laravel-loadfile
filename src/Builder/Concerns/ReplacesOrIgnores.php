<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait ReplacesOrIgnores
{
    private bool $replace = false;
    private bool $ignore = false;

    public function replace(bool $replace = true): self
    {
        $this->replace = $replace;

        if ($replace) {
            $this->ignore = false;
        }

        return $this;
    }

    public function ignore(bool $ignore = true): self
    {
        $this->ignore = $ignore;

        if ($ignore) {
            $this->replace = false;
        }

        return $this;
    }

    public function isReplace(): bool
    {
        return $this->replace;
    }

    public function isIgnore(): bool
    {
        return $this->ignore;
    }
}
