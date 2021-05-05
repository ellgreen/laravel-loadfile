# Laravel Load File 💽

A package to help with loading files into MySQL tables.

This uses MySQL's LOAD DATA statement to load text files quickly into your database.

This is usually **20 times** faster than using INSERT statements according to:
https://dev.mysql.com/doc/refman/8.0/en/insert-optimization.html

## Installation

**Requires Laravel 6 or above**

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

#### Manipulating data on load (set)

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
