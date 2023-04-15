<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasFields;
use EllGreen\LaravelLoadFile\Builder\Concerns\HasXmlRows;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HasXmlRowsTest extends TestCase
{
    /** @var MockObject&HasXmlRows */
    private $hasXmlRows;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasXmlRows = $this->getMockForTrait(HasXmlRows::class);
    }

    public function testSetRowIdentifiedBy(): void
    {
        $this->hasXmlRows->rowsIdentifiedBy('<tag>');

        $this->assertSame('<tag>', $this->hasXmlRows->getRowsIdentifiedBy());
    }
}
