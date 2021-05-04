<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait SetsValues
{
    private ?array $set = null;

    public function set(?array $set): self
    {
        $this->set = $set;
        return $this;
    }

    public function getSet(): ?array
    {
        return $this->set;
    }
}
