<?php

namespace Tests\Feature\Models;

use EllGreen\LaravelLoadFile\Laravel\Traits\LoadsFiles;
use Illuminate\Database\Eloquent\Model;

class TestUserNoOptions extends Model
{
    use LoadsFiles;
}
