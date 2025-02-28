<?php

namespace Tests\Feature;

use EllGreen\LaravelLoadFile\Builder\Builder;
use Tests\Feature\Models\TestUser;
use Tests\Feature\Models\TestUserNoOptions;

class LoadsFilesTraitTest extends TestCase
{
    public function test_get_builder(): void
    {
        $builder = TestUser::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertSame('test_users', $builder->getTable());
    }

    public function test_get_builder_with_options(): void
    {
        $builder = TestUser::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertTrue($builder->isReplace());
    }

    public function test_default_load_file_options(): void
    {
        $builder = TestUserNoOptions::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertFalse($builder->isReplace());
    }

    public function test_load_file_is_using_model_connection(): void
    {
        $builder = TestUser::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertSame('test-connection', $builder->getConnectionName());
    }

    public function test_load_file_is_using_model_connection_null(): void
    {
        $builder = TestUserNoOptions::loadFileBuilder('/path/to/test_users.csv', true);

        $this->assertNull($builder->getConnectionName());
    }

    public function test_load_file(): void
    {
        $this->instance(Builder::class, $builder = $this->createPartialMock(Builder::class, [
            'load',
        ]));

        $builder->method('load')->willReturn(true);

        $result = TestUser::loadFile('/path/to/employees.csv', true);
        $this->assertTrue($result);
    }
}
