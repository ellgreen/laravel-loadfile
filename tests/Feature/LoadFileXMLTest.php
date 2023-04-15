<?php

namespace Feature;

use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

class LoadFileXMLTest extends TestCase
{
    public function testSimpleLoad()
    {
        LoadFile::connection('mysql')
            ->xml(realpath(__DIR__ . '/../data/xml/people-simple.xml'), true)
            ->rowsIdentifiedBy('<person>')
            ->into('people')
            ->charset('utf8mb4')
            ->columns(['name', 'dob', 'greeting'])
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testLoadWithSet()
    {
        LoadFile::xml(realpath(__DIR__ . '/../data/xml/people.xml'), true)
            ->into('people')
            ->rowsIdentifiedBy('<person>')
            ->columns([DB::raw('@forename'), DB::raw('@surname'), 'dob'])
            ->set([
                'greeting' => 'Hello',
                'name' => DB::raw("concat(@forename, ' ', @surname)"),
                'imported_at' => DB::raw('current_timestamp'),
            ])
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

    public function testIgnoreRow()
    {
        LoadFile::xml(realpath(__DIR__ . '/../data/xml/people-simple.xml'), true)
            ->rowsIdentifiedBy('<person>')
            ->into('people')
            ->ignoreLines(1)
            ->columns(['name', 'dob', 'greeting'])
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

    public function testReplace()
    {
        LoadFile::xml(realpath(__DIR__ . '/../data/xml/people-simple.xml'), true)
            ->rowsIdentifiedBy('<person>')
            ->replace()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testIgnore()
    {
        LoadFile::xml(realpath(__DIR__ . '/../data/xml/people-simple.xml'), true)
            ->rowsIdentifiedBy('<person>')
            ->ignore()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testLowPriority(): void
    {
        LoadFile::xml(realpath(__DIR__ . '/../data/xml/people-simple.xml'), true)
            ->rowsIdentifiedBy('<person>')
            ->lowPriority()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->load();

        $this->assertJohnAndJaneExist();
    }

    public function testConcurrent(): void
    {
        LoadFile::xml(realpath(__DIR__ . '/../data/xml/people-simple.xml'), true)
            ->rowsIdentifiedBy('<person>')
            ->concurrent()
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->load();

        $this->assertJohnAndJaneExist();
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
