<?php

namespace Tests\Feature;

use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Illuminate\Support\Facades\DB;

class LoadFileTest extends TestCase
{
    public function testSimpleLoad(): void
    {
        LoadFile::connection('mysql')
            ->file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->into('people')
            ->charset('utf8mb4')
            ->columns(['name', 'dob', 'greeting'])
            ->fields(',', '"', '\\\\', true)
            ->lines('', '\\n')
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testLoadWithSet(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people.csv'), true)
            ->into('people')
            ->columns([DB::raw('@forename'), DB::raw('@surname'), 'dob'])
            ->set([
                'greeting' => 'Hello',
                'name' => DB::raw("concat(@forename, ' ', @surname)"),
                'imported_at' => DB::raw('current_timestamp'),
            ])
            ->fields(',', '"', '\\\\', true)
            ->load();

        $this->assertDatabaseHas('people', [
            'name' => 'John Doe',
            'dob' => '1980-01-01',
            'greeting' => 'Hello',
        ]);

        $this->assertDatabaseHas('people', [
            'name' => 'Jane Doe',
            'dob' => '1975-06-30',
            'greeting' => 'Hello',
        ]);
    }

    public function testIgnoreRow(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->into('people')
            ->ignoreLines(1)
            ->columns(['name', 'dob', 'greeting'])
            ->fields(',', '"', '\\\\', true)
            ->load();

        $this->assertDatabaseMissing('people', [
            'name' => 'John Doe',
            'dob' => '1980-01-01',
            'greeting' => 'Bonjour',
        ]);

        $this->assertDatabaseHas('people', [
            'name' => 'Jane Doe',
            'dob' => '1975-06-30',
            'greeting' => 'Hello',
        ]);
    }

    public function testReplace(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->replace()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->fields(',', '"', '\\\\', true)
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testIgnore(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->ignore()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->fields(',', '"', '\\\\', true)
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testLowPriority(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->lowPriority()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->fieldsTerminatedBy(',')
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testConcurrent(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->concurrent()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->fieldsTerminatedBy(',')
            ->load();

        $this->assertJohnAndJaneExist();
    }

    /**
     * Related to Issue #10
     * https://github.com/ellgreen/laravel-loadfile/issues/10
     */
    public function testFileWithNullStrings(): void
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people-with-null-strings.csv'), true)
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->fieldsTerminatedBy(',')
            ->load();

        $rows = DB::table('people')->pluck('greeting');

        $this->assertSame('null', $rows[0]);
        $this->assertSame('NULL', $rows[1]);
    }

    private function assertJohnAndJaneExist(): void
    {
        $this->assertDatabaseHas('people', [
            'name' => 'John Doe',
            'dob' => '1980-01-01',
            'greeting' => 'Bonjour',
        ]);

        $this->assertDatabaseHas('people', [
            'name' => 'Jane Doe',
            'dob' => '1975-06-30',
            'greeting' => 'Hello',
        ]);
    }
}
