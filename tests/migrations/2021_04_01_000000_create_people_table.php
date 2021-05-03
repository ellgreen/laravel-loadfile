<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('dob');
            $table->string('greeting');
            $table->timestamp('imported_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::drop('people');
    }
};
