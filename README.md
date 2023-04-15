# Laravel Load File 💽

[![Latest Stable Version](https://poser.pugx.org/ellgreen/laravel-loadfile/v)](//packagist.org/packages/ellgreen/laravel-loadfile)
[![License](https://poser.pugx.org/ellgreen/laravel-loadfile/license)](//packagist.org/packages/ellgreen/laravel-loadfile)

A package to help with loading files into MySQL tables.

This uses MySQL's LOAD DATA statement to load text files quickly into your database.

This is usually **20 times** faster than using INSERT statements according to:
https://dev.mysql.com/doc/refman/8.0/en/insert-optimization.html

### Options

This library currently can handle any of the options in a normal
LOAD DATA statement except for the partitioned table support. This will
be included in a future release of laravel-loadfile.

**Further information on the following options that can be passed to the
load file builder can be found here:**

https://dev.mysql.com/doc/refman/8.0/en/load-data.html

## Installation

**Requires > PHP 8.1 and > Laravel 9**

*Older versions of Laravel and PHP are supported through previous major versions of this library*

```bash
composer require ellgreen/laravel-loadfile
```

### Loading files

To use local files you will need to have `local_infile` enabled for
the client and server. To do this on the Laravel side you will need
to add the following to the `options` part of your database config:
```php
'config' => [
    PDO::MYSQL_ATTR_LOCAL_INFILE => true,
],
```

#### Simple file import

```php
use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;

LoadFile::file('/path/to/employees.csv', $local = true)
    ->into('employees')
    ->columns(['forename', 'surname', 'employee_id'])
    ->load();
```

#### XML file import

```php
use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;

LoadFile::xml('/path/to/employees.xml', $local = true)
    ->rowsIdentifiedBy('<tag-name>')
    ->into('employees')
    ->columns(['forename', 'surname', 'employee_id'])
    ->load();
```

#### Ignoring header row

```php
LoadFile::file('/path/to/employees.csv', $local = true)
    ->into('employees')
    ->columns(['forename', 'surname', 'employee_id'])
    ->ignoreLines(1)
    ->load();
```

#### Specifying field options

```php
LoadFile::file('/path/to/employees.csv', $local = true)
    ->into('employees')
    ->columns(['forename', 'surname', 'employee_id'])
    // like this
    ->fieldsTerminatedBy(',')
    ->fieldsEscapedBy('\\\\')
    ->fieldsEnclosedBy('"')
    // or
    ->fields(',', '\\\\', '"')
    ->load();
```

#### Specifying line options

```php
LoadFile::file('/path/to/employees.csv', $local = true)
    ->into('employees')
    ->columns(['forename', 'surname', 'employee_id'])
    // like this
    ->linesStartingBy('')
    ->linesTerminatedBy('\\n')
    // or
    ->lines('', '\\n')
    ->load();
```

#### Input preprocessing (set)

```php
LoadFile::file('/path/to/employees.csv', $local = true)
    ->into('employees')
    ->columns([
        DB::raw('@forename'),
        DB::raw('@surname'),
        'employee_id',
    ])
    ->set([
        'name' => DB::raw("concat(@forename, ' ', @surname)"),
    ])
    ->load();
```

#### Using a different connection

```php
LoadFile::connection('mysql')
    ->file('/path/to/employees.csv', $local = true)
    ->into('employees')
    ->columns(['forename', 'surname', 'employee_id'])
    ->load();
```

#### Duplicate-key and error handling

```php
LoadFile::connection('mysql')
    ->file('/path/to/employees.csv', $local = true)
    ->replace()
    // or
    ->ignore()
    ->into('employees')
    ->load();
```


## Loading data into Eloquent Models

Simply add the `LoadsFiles` trait to your model like so:

```php
use EllGreen\LaravelLoadFile\Laravel\Traits\LoadsFiles;

class User extends Model
{
    use LoadsFiles;
}
```

Then you can use the following method to load a file into that table:

```php
User::loadFile('/path/to/users.csv', $local = true);
```

### Need to specify options to load the file with?

Add the following method to your Model

```php
class User extends Model
{
    use LoadsFiles;

    public function loadFileOptions(Builder $builder): void
    {
        $builder
            ->fieldsTerminatedBy(',')
            ->ignoreLines(1);
    }
}
```

### Or you can get an instance of the query builder on the fly

```php
User::loadFileBuilder($file, $local)
    ->replace()
    ->ignoreLines(1)
    ->load();
```

## Development

### Unit tests

```bash
composer test-unit
```

### All tests

**You will need to have [docker](https://www.docker.com/) installed for these.**

```bash
composer check
```
