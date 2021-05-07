<?php

namespace EllGreen\LaravelLoadFile\Laravel\Traits;

use EllGreen\LaravelLoadFile\Builder\Builder;
use EllGreen\LaravelLoadFile\Exceptions\CompilationException;

trait LoadsFiles
{
    /**
     * @throws CompilationException
     */
    public static function loadFile(string $file, ?bool $local = null): bool
    {
        return static::loadFileBuilder($file, $local)->load();
    }

    public static function loadFileBuilder(string $file, ?bool $local = null): Builder
    {
        /** @var Builder $builder */
        $builder = app(Builder::class);

        $model = app(static::class);

        $builder->connection($model->getConnectionName())
            ->file($file, $local)
            ->into($model->getTable());

        $model->loadFileOptions($builder);

        return $builder;
    }

    public function loadFileOptions(Builder $builder): void
    {
        //
    }

    abstract public function getConnectionName();

    abstract public function getTable();
}
