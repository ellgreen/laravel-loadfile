<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait HasFields
{
    private ?string $fieldsTerminatedBy = null;
    private ?string $fieldsEnclosedBy = null;
    private bool $fieldsOptionallyEnclosed = false;
    private ?string $fieldsEscapedBy = null;

    public function fieldsTerminatedBy(?string $terminatedBy): self
    {
        $this->fieldsTerminatedBy = $terminatedBy;
        return $this;
    }

    public function fieldsEnclosedBy(?string $enclosedBy, ?bool $optionally = null): self
    {
        $this->fieldsEnclosedBy = $enclosedBy;

        if (isset($optionally)) {
            $this->fieldsOptionallyEnclosed($optionally);
        }

        return $this;
    }

    public function fieldsOptionallyEnclosed(bool $optionallyEnclosed): self
    {
        $this->fieldsOptionallyEnclosed = $optionallyEnclosed;
        return $this;
    }

    public function fieldsEscapedBy(?string $escapedBy): self
    {
        $this->fieldsEscapedBy = $escapedBy;
        return $this;
    }

    public function fields(
        ?string $terminatedBy,
        ?string $enclosedBy,
        ?string $escapedBy,
        ?bool $optionallyEnclosed = null
    ): self {
        $this->fieldsTerminatedBy($terminatedBy);
        $this->fieldsEnclosedBy($enclosedBy, $optionallyEnclosed);
        $this->fieldsEscapedBy($escapedBy);

        return $this;
    }

    public function getFieldsTerminatedBy(): ?string
    {
        return $this->fieldsTerminatedBy;
    }

    public function getFieldsEnclosedBy(): ?string
    {
        return $this->fieldsEnclosedBy;
    }

    public function getFieldsOptionallyEnclosed(): bool
    {
        return $this->fieldsOptionallyEnclosed;
    }

    public function getFieldsEscapedBy(): ?string
    {
        return $this->fieldsEscapedBy;
    }
}
