<?php

namespace EllGreen\LaravelLoadFile\Builder\Concerns;

trait HasXmlRows
{
    private string $rowIdentifier;

    /**
     * @param string $tag XML tag to identify rows, like: <item>
     */
    public function rowIdentifiedBy(string $tag): self
    {
        $this->rowIdentifier = $tag;
        return $this;
    }

    public function getRowIdentifier(): string
    {
        return $this->rowIdentifier;
    }
}
