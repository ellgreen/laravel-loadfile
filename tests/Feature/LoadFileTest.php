<?php

namespace Tests\Feature;

use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Illuminate\Support\Facades\DB;

class LoadFileTest extends TestCase
{
    public function testSimpleLoad()
    {
        LoadFile::connection('mysql')
            ->file(realpath(__DIR__ . '/../data/people-simple.csv'), true)
            ->into('people')
            ->columns(['name', 'dob', 'greeting'])
            ->fields(',', '"', '\\\\', true)
            ->lines('', '\\n')
            ->load();

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

    public function testLoadWithSet()
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

    public function testIgnoreRow()
    {
        LoadFile::file(realpath(__DIR__ . '/../data/people.csv'), true)
            ->into('people')
            ->ignore(1)
            ->columns([DB::raw('@forename'), DB::raw('@surname'), 'dob'])
            ->set([
                'greeting' => 'Hello',
                'name' => DB::raw("concat(@forename, ' ', @surname)"),
                'imported_at' => DB::raw('current_timestamp'),
            ])
            ->fields(',', '"', '\\\\', true)
            ->load();

        $this->assertDatabaseMissing('people', [
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
}
