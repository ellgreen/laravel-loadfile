<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Builder;

trait HasXmlRows
{
    private ?string $rowsIdentifiedBy = null;

    /**
     * @param ?string $rowsIdentifiedBy XML tag to identify rows, like: <item>
     */
    public function rowsIdentifiedBy(?string $rowsIdentifiedBy): self
    {
        $this->rowsIdentifiedBy = $rowsIdentifiedBy;
        return $this;
    }

    public function getRowsIdentifiedBy(): ?string
    {
        return $this->rowsIdentifiedBy;
    }
}
