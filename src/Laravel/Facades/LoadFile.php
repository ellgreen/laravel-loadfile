<?php

namespace EllGreen\LaravelLoadFile\Laravel\Facades;

use EllGreen\LaravelLoadFile\Builder\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Builder connection(?string $name = null)
 * @method static Builder file(string $file, ?bool $local = null)
 * @method static Builder into(string $table)
 *
 * @see Builder
 */
class LoadFile extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'load-file';
    }
}
