<?php

namespace Tests\Feature;

use EllGreen\LaravelLoadFile\Builder\Builder;
use Tests\Feature\Models\TestUser;
use Tests\Feature\Models\TestUserNoOptions;

class LoadsFilesTraitTest extends TestCase
{
    public function testGetBuilder(): void
    {
        $builder = TestUser::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertSame('test_users', $builder->getTable());
    }

    public function testGetBuilderWithOptions(): void
    {
        $builder = TestUser::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertTrue($builder->isReplace());
    }

    public function testDefaultLoadFileOptions()
    {
        $builder = TestUserNoOptions::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertFalse($builder->isReplace());
    }

    public function testLoadFile(): void
    {
        $this->instance(Builder::class, $builder = $this->createPartialMock(Builder::class, [
            'load',
        ]));

        $builder->method('load')->willReturn(true);

        $result = TestUser::loadFile('/path/to/employees.csv', true);
        $this->assertTrue($result);
    }
}
