<?php

namespace Tests\Unit\Builder\Concerns;

use EllGreen\LaravelLoadFile\Builder\Concerns\HasFields;
use PHPUnit\Framework\TestCase;

class HasFieldsTest extends TestCase
{
    /** @var HasFields */
    private $hasFields;

    protected function setUp(): void
    {
        $this->hasFields = new class
        {
            use HasFields;
        };
    }

    public function test_set_fields_terminated_by(): void
    {
        $this->hasFields->fieldsTerminatedBy(',');

        $this->assertSame(',', $this->hasFields->getFieldsTerminatedBy());
    }

    public function test_set_fields_enclosed_by(): void
    {
        $this->hasFields->fieldsEnclosedBy('"', $optionally = true);

        $this->assertSame('"', $this->hasFields->getFieldsEnclosedBy());
        $this->assertTrue($this->hasFields->getFieldsOptionallyEnclosed());
    }

    public function test_set_fields_escaped_by(): void
    {
        $this->hasFields->fieldsEscapedBy('\\\\');

        $this->assertSame('\\\\', $this->hasFields->getFieldsEscapedBy());
    }

    public function test_set_fields(): void
    {
        $this->hasFields->fields(',', '"', '\\\\', true);

        $this->assertSame(',', $this->hasFields->getFieldsTerminatedBy());
        $this->assertSame('"', $this->hasFields->getFieldsEnclosedBy());
        $this->assertSame('\\\\', $this->hasFields->getFieldsEscapedBy());
        $this->assertTrue($this->hasFields->getFieldsOptionallyEnclosed());
    }
}
