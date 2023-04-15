<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\FileType;

trait HasFile
{
    private ?string $file = null;
    private FileType $fileType = FileType::CSV;
    private bool $local = false;
    private ?string $table = null;
    private ?string $charset = null;
    private ?string $extension = null;

    public function file(string $file, ?bool $local = null): self
    {
        $this->file = $file;

        if (isset($local)) {
            $this->local($local);
        }

        return $this;
    }

    public function xml(string $file, ?bool $local = null): self
    {
        $this->file($file, $local)
            ->fileType(FileType::XML);

        return $this;
    }

    public function fileType(FileType $fileType): self
    {
        $this->fileType = $fileType;
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

    public function getFileType(): FileType
    {
        return $this->fileType;
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
