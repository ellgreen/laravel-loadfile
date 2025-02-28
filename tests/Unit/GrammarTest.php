<?php

namespace Tests\Unit;

use EllGreen\LaravelLoadFile\Builder\Builder;
use EllGreen\LaravelLoadFile\Exceptions\CompilationException;
use EllGreen\LaravelLoadFile\Grammar;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Expression;
use PHPUnit\Framework\TestCase;

class GrammarTest extends TestCase
{
    private Grammar $grammar;

    private Builder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $connection = $this->createMock(Connection::class);
        $this->grammar = new Grammar($connection);
        $this->builder = new Builder(
            $this->createMock(DatabaseManager::class),
        );

        $this->builder->file('/path/to/employees.csv', $local = true)
            ->into('employees')
            ->columns(['forename', 'surname', 'employee_id']);
    }

    public function test_no_file_throws_exception()
    {
        $this->expectException(CompilationException::class);

        $builder = (new Builder(
            $this->createMock(DatabaseManager::class),
        ))->into('table');

        $this->grammar->compileLoadFile($builder);
    }

    public function test_no_table_throws_exception()
    {
        $this->expectException(CompilationException::class);

        $builder = (new Builder(
            $this->createMock(DatabaseManager::class),
        ))->file('path/to/file');

        $this->grammar->compileLoadFile($builder);
    }

    public function test_no_table_or_file_throws_exception()
    {
        $this->expectException(CompilationException::class);

        $builder = new Builder(
            $this->createMock(DatabaseManager::class),
        );

        $this->grammar->compileLoadFile($builder);
    }

    public function test_simple_compile()
    {
        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_compile_with_no_columns()
    {
        $this->builder->columns(null);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv' into table `employees`
        SQL);
    }

    public function test_replace()
    {
        $this->builder->replace();

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv' replace
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_ignore()
    {
        $this->builder->ignore();

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv' ignore
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_low_priority()
    {
        $this->builder->lowPriority();

        $this->assertSqlAndBindings(<<<'SQL'
            load data low_priority local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_concurrent()
    {
        $this->builder->concurrent();

        $this->assertSqlAndBindings(<<<'SQL'
            load data concurrent local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_ignore_lines()
    {
        $this->builder->ignoreLines(1);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` ignore 1 lines
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_fields_terminated_by()
    {
        $this->builder->fieldsTerminatedBy(',');

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` fields terminated by ','
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_fields()
    {
        $this->builder->fields(',', '"', '\\\\', true);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` fields terminated by ','
            optionally enclosed by '"' escaped by '\\'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_lines()
    {
        $this->builder->lines('', '\\n');

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees`
            lines starting by '' terminated by '\n'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_compile_with_set()
    {
        $this->builder->set([
            'name' => new Expression("concat(@forename, ' ', @surname)"),
            'department' => 'sales',
        ]);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
            set `name` = concat(@forename, ' ', @surname), `department` = ?
        SQL, ['sales']);
    }

    public function test_compile_with_charset()
    {
        $this->builder->charset('utf8mb4');

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` character set 'utf8mb4'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function test_compile_with_all_options()
    {
        $this->builder
            ->replace()
            ->charset('utf8mb4')
            ->fields(',', '"', '\\\\', true)
            ->lines('', '\\n')
            ->ignoreLines(1)
            ->set([
                'name' => new Expression("concat(@forename, ' ', @surname)"),
                'department' => 'sales',
            ]);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            replace into table `employees` character set 'utf8mb4'
            fields terminated by ',' optionally enclosed by '"' escaped by '\\'
            lines starting by '' terminated by '\n'
            ignore 1 lines
            (`forename`, `surname`, `employee_id`)
            set `name` = concat(@forename, ' ', @surname), `department` = ?
        SQL, ['sales']);
    }

    private function assertSqlAndBindings(string $expectedSql, ?array $expectedBindings = null): void
    {
        $expectedSql = trim(
            str_replace('    ', '', str_replace("\n", ' ', $expectedSql))
        );

        $query = $this->grammar->compileLoadFile($this->builder);

        $this->assertSame($expectedSql, $query->getSql());

        if (isset($expectedBindings)) {
            $this->assertSame($expectedBindings, $query->getBindings());

            return;
        }

        $this->assertEmpty($query->getBindings());
    }
}
