<?php

namespace EllGreen\LaravelLoadFile\Exceptions;

use Exception;

class CompilationException extends Exception
{
    public static function noFileOrTableSupplied(): self
    {
        return new self('File and Table must be set to compile load data query');
    }
}
