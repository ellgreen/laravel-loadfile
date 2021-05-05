<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait HasFile
{
    private ?string $file = null;
    private bool $local = false;
    private ?string $table = null;
    private ?string $charset = null;

    public function file(string $file, ?bool $local = null): self
    {
        $this->file = $file;

        if (isset($local)) {
            $this->local($local);
        }

        return $this;
    }

    public function local(bool $local): self
    {
        $this->local = $local;
        return $this;
    }

    public function into(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function charset(?string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function isLocal(): bool
    {
        return $this->local;
    }

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }
}
