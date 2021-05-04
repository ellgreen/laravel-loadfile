<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HasFieldsTest extends TestCase
{
    /** @var MockObject|HasFields */
    private MockObject $hasFields;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasFields = $this->getMockForTrait(HasFields::class);
    }

    public function testSetFieldsTerminatedBy()
    {
        $this->hasFields->fieldsTerminatedBy(',');

        $this->assertSame(',', $this->hasFields->getFieldsTerminatedBy());
    }

    public function testSetFieldsEnclosedBy()
    {
        $this->hasFields->fieldsEnclosedBy('"', $optionally = true);

        $this->assertSame('"', $this->hasFields->getFieldsEnclosedBy());
        $this->assertTrue($this->hasFields->getFieldsOptionallyEnclosed());
    }

    public function testSetFieldsEscapedBy()
    {
        $this->hasFields->fieldsEscapedBy('\\\\');

        $this->assertSame('\\\\', $this->hasFields->getFieldsEscapedBy());
    }

    public function testSetFields()
    {
        $this->hasFields->fields(',', '"', '\\\\', true);

        $this->assertSame(',', $this->hasFields->getFieldsTerminatedBy());
        $this->assertSame('"', $this->hasFields->getFieldsEnclosedBy());
        $this->assertSame('\\\\', $this->hasFields->getFieldsEscapedBy());
        $this->assertTrue($this->hasFields->getFieldsOptionallyEnclosed());
    }
}
